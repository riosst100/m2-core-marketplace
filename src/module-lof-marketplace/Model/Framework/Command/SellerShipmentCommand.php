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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Framework\Command;

use Magento\Sales\Model\Order;
use Lof\MarketPlace\Model\Orderitems;

/**
 * SellerShipmentCommand
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @see \Lof\MarketPlace\Observer\OrderShipment
 */
class SellerShipmentCommand implements SellerShipmentCommandInterface
{
    const DEFAULT_INVOICE_STATE = "Pending";

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\SellerProduct
     */
    protected $sellerProduct;

    /**
     * @var \Lof\MarketPlace\Model\CalculateCommission
     */
    protected $calculate;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|null
     */
    protected $_dateTime = null;

    /**
     * OrderShipment constructor.
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerProduct
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->calculate = $calculate;
        $this->helper = $helper;
        $this->sellerProduct = $sellerProduct;
        $this->sender = $sender;
        $this->_dateTime = $dateTime;
    }

    /**
     * @inheritdoc
     */
    public function execute($order, int $sellerId, $items = []): bool
    {
        try {
            $seller = $this->helper->getSellerById($sellerId);
            $sellerInvoice = $this->getSellerInvoice($order->getId(), $sellerId);
            $sellerInvoiceId = $sellerInvoice ? $sellerInvoice->getInvoiceId() : "";

            $countSale = 0;
            $sellerOrderItemsCollection = $this->getSellerOrderItemsCollection($order->getId(), $sellerId);
            /** update seller order items*/
            foreach ($sellerOrderItemsCollection as $key => $orderitem) {
                $countSale = $countSale + (int)$orderitem->getQtyInvoiced();
                $orderitem->setStatus(Orderitems::STATE_COMPLETED);
                $orderitem->save();
            }
            /** update seller order*/
            $sellerOrderCollection = $this->getSellerOrderCollection($order->getId(), $sellerId);
            foreach ($sellerOrderCollection as $key => $_sellerOrder) {
                $_sellerOrder->setIsShiped($countSale);
                $_sellerOrder->setStatus(Order::STATE_COMPLETE);
                $_sellerOrder->save();

                $description = __('Amount from order') . ' #' . $_sellerOrder->getOrderId() . ',' . __('invoice') . ' #'
                    . $sellerInvoiceId;

                try {
                    $isOfflineMethod = $order->getPayment()->getMethodInstance()->isOffline();
                } catch (\Exception $e){
                    $isOfflineMethod = false;
                }

                $this->updateSellerAmount(
                    $sellerId,
                    $_sellerOrder->getSellerAmount(),
                    $description,
                    $isOfflineMethod
                );

                $this->sendShipmentEmail($seller, $_sellerOrder->getOrderId());
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * get current date time
     *
     * @return string
     */
    public function getCurrentDateTime()
    {
        if (!$this->_dateTime) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_dateTime = $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        }
        return $this->_dateTime->gmtDate();
    }

    /**
     * @param int $updateSellerId
     * @param float|int $totalAmount
     * @param string $description
     * @param bool $isPendingCommissions
     * @return void
     * @throws \Exception
     */
    public function updateSellerAmount($updateSellerId, $totalAmount, $description, $isOfflineMethod = false)
    {
        if (!$this->helper->getConfig('general_settings/commission_approval')){
            $isOfflineMethod = false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerModel = $objectManager->create(\Lof\MarketPlace\Model\Amount::class);
        $sellerAmountModel = $sellerModel->load($updateSellerId, 'seller_id');
        $remainingAmount = $sellerAmountModel->getAmount();
        $totalRemainingAmount = $remainingAmount;
        $status = 0;
        if (!$isOfflineMethod){
            $totalRemainingAmount = $remainingAmount + $totalAmount;
            $sellerAmountModel->setSellerId($updateSellerId)->setAmount($totalRemainingAmount);
            $sellerAmountModel->save();
            $status = 1;
        }
        $this->createAmountTransaction($updateSellerId, $totalAmount, $totalRemainingAmount, $description, $status);
    }

    /**
     * @param $sellerId
     * @param $amount
     * @param $remainingAmount
     * @param $description
     * @param $status
     * @return void
     * @throws \Exception
     */
    public function createAmountTransaction($sellerId, $amount, $remainingAmount, $description, $status)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $amountTransaction = $objectManager->create(\Lof\MarketPlace\Model\Amounttransaction::class);
        $amountTransaction
            ->setSellerId($sellerId)
            ->setAmount($amount)
            ->setBalance($remainingAmount)
            ->setDescription($description)
            ->setStatus($status);
        $amountTransaction->save();
    }

    /**
     * Get seller order collection
     *
     * @param mixed $seller
     * @param int $orderId
     * @return void
     */
    public function sendShipmentEmail($seller, $orderId)
    {
        if ($this->helper->getConfig('email_settings/enable_send_email')) {
            $data = [];
            $data['email'] = $seller->getData('email');
            $data['name'] = $seller->getData('name');
            $data['order_id'] = $orderId;
            $data['order_status'] = Order::STATE_COMPLETE;

            $this->sender->newShipment($data);
        }
    }

    /**
     * Get seller order collection
     *
     * @param int $orderId
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\ResourceModel\Order\Collection|mixed
     */
    protected function getSellerOrderCollection($orderId, $sellerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create(\Lof\MarketPlace\Model\ResourceModel\Order\Collection::class);
        $collection->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId);
        return $collection;
    }

    /**
     * Get seller order items collection
     *
     * @param int $orderId
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\ResourceModel\Orderitems\Collection|mixed
     */
    protected function getSellerOrderItemsCollection($orderId, $sellerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create(\Lof\MarketPlace\Model\ResourceModel\Orderitems\Collection::class);
        $collection->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId);
        return $collection;
    }

    /**
     * Get seller invoice
     *
     * @param int $orderId
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Invoice|mixed|null
     */
    protected function getSellerInvoice($orderId, $sellerId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create(\Lof\MarketPlace\Model\ResourceModel\Invoice\Collection::class);
        $collection->addFieldToFilter('seller_order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId);

        return $collection->getFirstItem();
    }
}
