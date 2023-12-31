<?php

namespace Lofmp\SplitOrderPaypal\Plugin\MarketPlace\Block\Sale\Order;

use Lof\MarketPlace\Block\Sale\Order\View;
use Lofmp\SplitOrderPaypal\Helper\Data;
use Magento\Sales\Model\Order;

class ViewPlugin
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param View $subject
     * @param Order $result
     * @return Order
     */
    public function afterGetOrder(
        View $subject,
        Order $order
    ) {
        if ($order->getppParentOrderId()) {
            $paypalMainOrder = $this->helperData->getPaypalMainOrder($order->getppParentOrderId());
            if ($paypalMainOrder) {
                $order->setPayment($paypalMainOrder->getPayment());
            }
        }
        return $order;
    }
}
