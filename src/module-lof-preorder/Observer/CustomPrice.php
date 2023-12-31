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

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory as Items;
use Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Products;

class CustomPrice implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Lof\PreOrder\Model\ItemFactory
     */
    protected $_item;

    /**
     * @var \Lof\PreOrder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @var Items
     */
    protected $_itemCollection;

    /**
     * @var CollectionFactory
     */
    protected $_preorderCollection;

    /**
     * @var Products
     */
    protected $_productCollection;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @param RequestInterface                            $request
     * @param \Magento\Customer\Model\Session             $customerSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Lof\PreOrder\Model\ItemFactory             $item
     * @param \Lof\PreOrder\Helper\Data                   $preorderHelper
     * @param Items                                       $itemCollection
     * @param CollectionFactory                           $preorderCollection
     * @param Products                                    $productCollection
     * @param \Magento\Checkout\Model\CartFactory         $cart
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lof\PreOrder\Model\ItemFactory $item,
        \Lof\PreOrder\Helper\Data $preorderHelper,
        Items $itemCollection,
        CollectionFactory $preorderCollection,
        Products $productCollection,
        \Magento\Checkout\Model\CartFactory $cart
    ) {

        $this->_request            = $request;
        $this->_customerSession    = $customerSession;
        $this->_messageManager     = $messageManager;
        $this->_item               = $item;
        $this->_preorderHelper     = $preorderHelper;
        $this->_itemCollection     = $itemCollection;
        $this->_preorderCollection = $preorderCollection;
        $this->_productCollection  = $productCollection;
        $this->_cart               = $cart;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $helper                    = $this->_preorderHelper;
            $item                      = $observer->getEvent()->getData('quote_item');
            $product                   = $observer->getEvent()->getData('product');
            $productId                 = $this->getFinalProductId($product);
            $preorderCompleteProductId = $helper->getPreorderCompleteProductId();
            if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
                //Do nothing
            } elseif ($product->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
                $helper->writeLog("Error 1", ($product->getId() . " - " . $item->getId()));
            } else {
                $cart_warning_msg = $helper->getMsgWarningQtyInCart($productId, $product->getname(), $item->getQty());
                if ($cart_warning_msg) {
                    $item->setMessage($cart_warning_msg);
                }

                $this->setPreorderPrice($item, $product, $productId);

                if ($productId == $preorderCompleteProductId) {
                    $result = $this->processPreorderCompleteData($item);
                    if ($result['error']) {
                        $this->_messageManager->addNotice(__($result['msg']));
                        throw new \Magento\Framework\Exception\LocalizedException($result['msg']);
                    }
                }

                if ($helper->isPreorder($productId)) {
                    $msg = $helper->getPreOrderInfoBlock($productId, $product);
                    if ($msg) {
                        $item->setPreorderMsg($msg);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_messageManager->addError($e->getMessage());
        }
    }

    /**
     * Get Product Id
     *
     * @param object $product
     *
     * @return int
     */
    public function getFinalProductId($product)
    {
        $helper    = $this->_preorderHelper;
        $productId = $product->getId();
        $data      = $this->_request->getParams();
        if (array_key_exists('selected_configurable_option', $data)) {
            if ($data['selected_configurable_option'] != '') {
                $productId = $data['selected_configurable_option'];
            } else {
                if (array_key_exists('super_attribute', $data)) {
                    $info      = $data['super_attribute'];
                    $productId = $helper->getAssociatedId($info, $product);
                }
            }
        }

        return $productId;
    }

    /**
     * Set Preorder Product Price
     *
     * @param object $item
     * @param object $product
     * @param int    $productId
     *
     * @return int
     */
    public function setPreorderPrice($item, $product, $productId)
    {
        $helper          = $this->_preorderHelper;
        $preorderPercent = $helper->getPreorderPercent($productId, $product);

        if ($helper->isPartialPreorder($productId)) {
            $id    = (int) $item->getId();
            $item  = ($item->getParentItem() ? $item->getParentItem() : $item);
            $price = $helper->getPreorderPrice($product, $productId);
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
            if ($id > 0) {
                $collection = $this->_itemCollection->create();
                $item       = $helper->getDataByField($id, 'item_id', $collection);
                if ($item) {
                    $data = [
                        'item_id'          => $id,
                        'preorder_percent' => $preorderPercent,
                    ];

                    $this->_item->create()
                                ->addData($data)
                                ->setId($item->getId())
                                ->save();
                }
            }
        }
    }

    /**
     * Get Product Id
     *
     * @param object $quoteItem
     * @param mixed  $qty
     * @return array
     */
    public function processPreorderCompleteData($quoteItem, $qty = null)
    {
        $itemId = (int) $quoteItem->getId();
        $helper = $this->_preorderHelper;
        if (! $this->_customerSession->isLoggedIn()) {
            $msg    = 'There was some error while processing your request.';
            $result = ['error' => true, 'msg' => $msg];

            return $result;
        }
        $data = $this->_request->getParams();
        if (! $qty) {
            $qty = $data['qty'];
        }
        $qty               = (int) $qty;
        $orderId           = $data['order_id'];
        $orderItemId       = $data['item_id'];
        $preorderProductId = $data['pro_id'];
        $stockStatus       = 0;
        $preorderQty       = 0;
        $collection        = $this->_productCollection->create();
        $table             = 'cataloginventory_stock_item';
        $bind              = 'product_id = entity_id';
        $cond              = '{{table}}.stock_id = 1';
        $type              = 'left';
        $alias             = 'is_in_stock';
        $field             = 'is_in_stock';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $alias = 'qty';
        $field = 'qty';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $collection->addFieldToFilter('entity_id', $preorderProductId);
        foreach ($collection as $value) {
            $stockStatus = $value->getIsInStock();
            $preorderQty = $value->getQty();
        }
        if ($stockStatus == 0 || $qty > $preorderQty) {
            $msg    = 'Product is not available.';
            $result = ['error' => true, 'msg' => $msg];

            return $result;
        }
        if ($itemId > 0) {
            $msg    = 'Already added to cart.';
            $result = ['error' => true, 'msg' => $msg];

            return $result;
        }
        $collection = $this->_preorderCollection->create();
        $values     = [$orderItemId, $orderId];
        $fields     = ['item_id', 'order_id'];
        $item       = $helper->getDataByField($values, $fields, $collection);
        if ($item) {
            $remainingAmount = $item->getRemainingAmount();
            $unitPrice       = $remainingAmount;
            $quoteItem->setCustomPrice($unitPrice);
            $quoteItem->setOriginalCustomPrice($unitPrice);
            $quoteItem->getProduct()->setIsSuperMode(true);

            return ['error' => false];
        }
        $msg    = 'Something went wrong.';
        $result = ['error' => true, 'msg' => $msg];

        return $result;
    }
}
