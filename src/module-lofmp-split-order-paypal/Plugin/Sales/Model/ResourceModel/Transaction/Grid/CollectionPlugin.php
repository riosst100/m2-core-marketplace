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
 * @package    Lofmp_SplitOrderPaypal
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrderPaypal\Plugin\Sales\Model\ResourceModel\Transaction\Grid;

use Lofmp\SplitOrderPaypal\Helper\Data;
use Magento\Sales\Model\ResourceModel\Transaction\Grid\Collection as TransactionGridCollection;

class CollectionPlugin
{
    /**
     * @var \Magento\Sales\Model\OrderFactory|null
     */
    protected $orderFactory;

    /**
     * @param Data $helperData
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param TransactionGridCollection $collection
     * @param $order
     * @return array
     */
    public function beforeSetOrderFilter(
        TransactionGridCollection $collection,
        $order
    ) {
        if ($order instanceof \Magento\Sales\Model\Order) {
            if ($order->getPpParentOrderId()) {
                $parentOrderModel = $this->orderFactory->create()->load($order->getPpParentOrderId());
                $parentOrder = $parentOrderModel ?: $order;
            } else {
                $parentOrder = $order;
            }
        } else {
            $orderModel = $this->orderFactory->create()->load($order);
            if ($orderModel->getPpParentOrderId()) {
                $parentOrderModel = $this->orderFactory->create()->load($orderModel->getPpParentOrderId());
                $parentOrder = $parentOrderModel ? $parentOrderModel->getId() : $order;
            } else {
                $parentOrder = $order;
            }
        }
        return [$parentOrder];
    }

    /**
     * @param TransactionGridCollection $collection
     * @param $orderId
     * @return array
     */
    public function beforeAddOrderIdFilter(
        TransactionGridCollection $collection,
        $orderId
    ) {
        $order = $this->orderFactory->create()->load($orderId);
        if ($order->getPpParentOrderId()) {
            $parentOrderModel = $this->orderFactory->create()->load($order->getPpParentOrderId());
            if ($parentOrderModel) {
                return [$parentOrderModel->getId()];
            }
        }
        return [$orderId];
    }
}
