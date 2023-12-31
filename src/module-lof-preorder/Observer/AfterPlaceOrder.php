<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lof\PreOrder\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory as Items;
use Lof\PreOrder\Model\ResourceModel\Complete\CollectionFactory;
use Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory as Preorder;

class AfterPlaceOrder implements ObserverInterface
{
    /**
     * @var \Lof\PreOrder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @var \Lof\PreOrder\Model\PreOrderFactory
     */
    protected $_preorder;

    /**
     * @var Items
     */
    protected $_itemCollection;

    /**
     * @var CollectionFactory
     */
    protected $_completeCollection;

    /**
     * @var Preorder
     */
    protected $_preorderCollection;

    /**
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     * @param \Lof\PreOrder\Model\PreOrderFactory $preorder
     * @param Items $itemCollection
     * @param CollectionFactory $completeCollection
     * @param Preorder $preorderCollection
     */
    public function __construct(
        \Lof\PreOrder\Helper\Data $preorderHelper,
        \Lof\PreOrder\Model\PreOrderFactory $preorder,
        Items $itemCollection,
        CollectionFactory $completeCollection,
        Preorder $preorderCollection
    ) {
        $this->_preorderHelper = $preorderHelper;
        $this->_preorder = $preorder;
        $this->_itemCollection = $itemCollection;
        $this->_completeCollection = $completeCollection;
        $this->_preorderCollection = $preorderCollection;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $orders = $observer->getEvent()->getData('orders');
        if ($orders) {
            foreach ($orders as $order) {
                $this->setPreOrder($order);
            }
        } else {
            $order = $observer->getEvent()->getData('order');
            $this->setPreOrder($order);
        }
    }

    /**
     * @param $order
     *
     */
    private function setPreOrder($order)
    {
        $orderId = $order->getId();
        $order = $this->_preorderHelper->getOrder($orderId);
        $orderedItems = $order->getAllItems();
        foreach ($orderedItems as $item) {
            $this->setPreorderData($item, $order);
            $this->setPreorderCompleteData($item);
        }
    }

    /**
     * Set Preorder Price and Data in Table
     *
     * @param object $item
     * @param object $order
     */
    public function setPreorderData($item, $order)
    {
        $helper = $this->_preorderHelper;
        $preorderType = $helper->getPreorderType();
        $time = time();
        $customerId = (int)$order->getCustomerId();
        $customerEmail = $order->getCustomerEmail();
        $remainingAmount = 0;
        $preorderPercent = '';
        $parent = ($item->getParentItem() ? $item->getParentItem() : $item);
        $parentId = $parent->getProductId();
        $productId = $item->getProductId();
        $quoteItemId = $item->getQuoteItemId();
        if ($parentId == $productId) {
            $parentId = 0;
        }
        if ($helper->isPreorder($productId)) {
            $orderItemId = $item->getId();
            $parentItemId = $item->getParentItemId();
            $qty = $item->getQtyOrdered();
            $price = $parent->getPrice();
            if ($helper->isPartialPreorder($productId)) {
                $collection = $this->_itemCollection->create();
                $collection = $collection->addFieldToFilter("item_id", (int)$quoteItemId);
                $item = null;
                if ($collection->getSize()) {
                    $item = $collection->getFirstItem();
                }
                if ($item) {
                    $preorderPercent = $item->getPreorderPercent();
                    $totalPrice = ($price * 100) / $preorderPercent;
                    $remainingAmount = $totalPrice - $price;
                } else {
                    //get product info and price to calculate remaining Amount
                    $product = $helper->getProduct($productId);
                    $preorderPercent = $helper->getPreorderPercent($productId, $product);
                    $totalPrice = $helper->getPrice($product);
                    if ((float)$totalPrice > (float)$price) {
                        $remainingAmount = (float)$totalPrice - (float)$price;
                    }
                }
            }
            $preorderItemData = [
                'order_id' => $order->getId(),
                'item_id' => $orderItemId,
                'product_id' => $productId,
                'parent_id' => $parentId,
                'customer_id' => $customerId,
                'customer_email' => $customerEmail,
                'preorder_percent' => $preorderPercent,
                'paid_amount' => $price,
                'remaining_amount' => $remainingAmount,
                'qty' => $qty,
                'type' => $preorderType,
                'status' => 0,
                'time' => $time,
            ];
            $this->_preorder->create()->setData($preorderItemData)->save();
        }
    }

    /**
     * Set Preorder Complete Price and Data in Table
     *
     * @param object $orderItem
     */
    public function setPreorderCompleteData($orderItem)
    {
        $helper = $this->_preorderHelper;
        $quoteItemId = $orderItem->getQuoteItemId();
        $productId = $orderItem->getProductId();
        $preorderCompleteProductId = $helper->getPreorderCompleteProductId();
        if ($productId == $preorderCompleteProductId) {
            $id = 0;
            $collection = $this->_completeCollection->create();
            $value = $quoteItemId;
            $field = 'quote_item_id';
            $item = $helper->getDataByField($value, $field, $collection);
            if ($item) {
                $itemId = $item->getOrderItemId();
                $collection = $this->_preorderCollection->create();
                $field = 'item_id';
                $item = $helper->getDataByField($itemId, $field, $collection);
                if ($item) {
                    $remainingAmount = $item->getRemainingAmount();
                    $paidAmount = $item->getPaidAmount();
                    $totalAmount = $paidAmount + $remainingAmount;
                    $item->setStatus(1)
                        ->setRemainingAmount(0)
                        ->setPaidAmount($totalAmount)
                        ->setId($item->getId())
                        ->save();
                }
            }
        }
    }
}
