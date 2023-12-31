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

use Lof\MarketPlace\Model\Order as SellerOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerRefundCommand implements SellerRefundCommandInterface
{
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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|null
     */
    protected $_dateTime = null;

    /**
     * OrderRefund constructor.
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerProduct
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct
    ) {
        $this->calculate = $calculate;
        $this->helper = $helper;
        $this->sellerProduct = $sellerProduct;
    }

    /**
     * @inheritdoc
     */
    public function execute($creditmemo, int $sellerId, $items = []): bool
    {
        try {
            $order = $creditmemo->getOrder();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderRefund = $objectManager->create(\Lof\MarketPlace\Model\Refund::class);
            $orderSeller = $this->getSellerOrder($sellerId, $order->getId());
            /** Dont run when seller order is not exists */
            if (!$orderSeller) {
                return true;
            }
            $sellerOrderId = $order->getId();
            $sellerShippingAmount = $creditmemo->getShippingAmount();
            $refundData = $this->initRefundData($sellerOrderId, $sellerId, $sellerShippingAmount, $creditmemo);

            foreach ($items as $key => $item) {
                if (abs($item->getData('price')) > 0) {
                    $refundData['grand_total'] += $refundData['subtotal']
                        + $refundData['shipping_amount']
                        + $refundData['tax_amount']
                        - $refundData['discount_amount'];
                    $refundData['base_grand_total'] += $refundData['base_subtotal']
                        + $refundData['base_shipping_amount']
                        + $refundData['base_tax_amount']
                        - $refundData['base_discount_amount'];
                    $refundData['total_qty'] += $item->getData('qty');

                    $commission = $this->helper->getCommission($sellerId, $item->getProductId());
                    $priceCommission = $this->calculate->calculate($commission, $item);

                    $orderItemsCollection = $this->getSellerOrderItems($order->getId(), $sellerId, $item->getSku());

                    /** Update seller order item commission refunded, qty refunded */
                    foreach ($orderItemsCollection as $key => $order_item) {
                        $seller_commission = $priceCommission + $order_item->getData('seller_commission_refund');
                        $adminCommission = $item->getData('row_total')
                            + $item->getData('tax_amount')
                            - $item->getData('discount_amount')
                            - $priceCommission
                            + $order_item->getData('admin_commission_refund');
                        $qtyRefunded = $order_item->getData('qty_refunded') + $item->getData('qty');
                        $order_item->setQtyRefunded($qtyRefunded)
                            ->setAdminCommissionRefund($adminCommission)
                            ->setSellerCommissionRefund($seller_commission);
                        if ((int)$order_item->getProductQty() >= $qtyRefunded) {
                            $order_item->save();
                        }
                    }
                    $refundData['refunded'] += $priceCommission;
                }
            }

            $refundDataObj = new \Magento\Framework\DataObject($refundData);
            $refundData = $refundDataObj->getData();
            $orderRefund->setData($refundData)->save();

            $sellerData = $this->helper->getSellerById($sellerId);
            $description = __('Refund from order') . ' #' . $sellerOrderId;
            $refund = -($refundData['refunded'] + $refundData['shipping_amount']);
            $countRefund = $orderSeller->getIsRefunded() + $refundData['total_qty'];
            $sale = $sellerData->getSale() - $refundData['total_qty'];
            $sellerAmount = $orderSeller->getSellerAmount() - $refundData['refunded'] - $refundData['shipping_amount'];

            /** Update order status and count refund */
            if ($countRefund == $orderSeller->getIsInvoiced()) {
                $orderSeller->setStatus(SellerOrder::STATE_CLOSED)
                    ->setSellerAmount($sellerAmount)
                    ->setIsRefunded($countRefund)
                    ->save();
            } else {
                $orderSeller->setSellerAmount($sellerAmount)
                    ->setIsRefunded($countRefund)
                    ->save();
            }

            /** Update seller amount */
            $this->updateSellerAmount($sellerId, $refund, $description);

            /** Update seller sale, total sold */
            $totalSold = $sellerData->getData('total_sold') + $refund;
            $sellerData->setSale($sale)
                ->setTotalSold($totalSold)
                ->save();
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Get order by order id
     *
     * @param int $sellerId
     * @param int $orderId
     * @return \Lof\MarketPlace\Model\Order|mixed|object|null
     */
    protected function getSellerOrder($sellerId, $orderId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerOrderModel = $objectManager->create(\Lof\MarketPlace\Model\Order::class);
        $ordersellerCollection = $objectManager->create(\Lof\MarketPlace\Model\ResourceModel\Order\Collection::class);
        $ordersellerCollection
            ->addFieldToFilter("seller_id", $sellerId)
            ->addFieldToFilter("order_id", $orderId);
        $orderSellerData = $ordersellerCollection->getFirstItem();

        if ($orderSellerData && $orderSellerData->getId()) {
            $sellerOrderModel = $sellerOrderModel->load((int)$orderSellerData->getId());
        } else {
            $sellerOrderModel = null;
        }
        return $sellerOrderModel;
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
     * get seller order items
     *
     * @param int $orderId
     * @param int $sellerId
     * @param string|null $sku
     * @return \Lof\MarketPlace\Model\ResourceModel\Orderitems\Collection|mixed|object
     */
    protected function getSellerOrderItems($orderId, $sellerId, $sku = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->create(\Lof\MarketPlace\Model\ResourceModel\Orderitems\Collection::class);
        $collection->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId);

        if ($sku) {
            $collection->addFieldToFilter('product_sku', $sku);
        }

        return $collection;
    }

    /**
     * Init refund data
     *
     * @param int $sellerOrderId
     * @param int $sellerId
     * @param float|int $sellerShippingAmount
     * @param mixed $creditmemo
     * @return mixed|array
     */
    public function initRefundData($sellerOrderId, $sellerId, $sellerShippingAmount, $creditmemo )
    {
        $currentTimestamp = $this->getCurrentDateTime();
        $refundData = [
            'seller_id' => $sellerId,
            'order_id' => $sellerOrderId,
            'creditmemo_id' => $creditmemo->getId(),
            'state' => $creditmemo->getState(),
            'status' => __('Refunded'),
            'subtotal' => 0,
            'base_subtotal' => 0,
            'tax_amount' => 0,
            'base_tax_amount' => 0,
            'shipping_tax_amount' => 0,
            'base_shipping_tax_amount' => 0,
            'discount_amount' => 0,
            'base_discount_amount' => 0,
            'shipping_amount' => $sellerShippingAmount,
            'base_shipping_amount' => 0,
            'subtotal_incl_tax' => 0,
            'base_subtotal_incl_tax' => 0,
            'total_qty' => 0,
            'updated_at' => $currentTimestamp,
            'shipping_incl_tax' => 0,
            'base_shipping_incl_tax' => 0,
            'grand_total' => 0,
            'base_grand_total' => 0,
            'base_total_refunded' => 0,
            'refunded' => 0
        ];
        return $refundData;
    }

    /**
     * Get commission value
     *
     * @param float $commission
     * @param float $productPrice
     * @param int $productQty
     *
     * @return float
     */
    public function getCommissionValue($commissionProduct, $productPrice)
    {
        if ($commissionProduct != 0) {
            $commissionPerProduct = $productPrice * ($commissionProduct / 100);
            $commission = $commissionPerProduct;
        } else {
            $commission = 0;
        }
        return $commission;
    }

    /**
     * Update seller amount
     *
     * @param int $updateSellerId
     * @param float|double $totalAmount
     *
     * @return float|double
     */
    public function updateSellerAmount($updateSellerId, $totalAmount, $description)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerAmountModel = $objectManager->get(\Lof\MarketPlace\Model\Amount::class);
        $amountTransaction = $objectManager->get(\Lof\MarketPlace\Model\Amounttransaction::class);
        $date = $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        $sellerDetails = $sellerAmountModel->load($updateSellerId, 'seller_id');
        $remainingAmount = $sellerDetails->getAmount();
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        $amountTransaction->setSellerId($updateSellerId)
            ->setAmount($totalAmount)
            ->setBalance($totalRemainingAmount)
            ->setDescription($description)
            ->setUpdatedAt($date->gmtDate());
        $sellerDetails->setSellerId($updateSellerId)
            ->setAmount($totalRemainingAmount);
        $sellerDetails->save();
        $amountTransaction->save();
    }
}
