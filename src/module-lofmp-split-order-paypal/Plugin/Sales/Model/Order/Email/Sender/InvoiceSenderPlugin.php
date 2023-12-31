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

namespace Lofmp\SplitOrderPaypal\Plugin\Sales\Model\Order\Email\Sender;

use Lofmp\SplitOrderPaypal\Helper\Data;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;

class InvoiceSenderPlugin
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @param InvoiceSender $subject
     * @param callable $proceed
     * @param Invoice $invoice
     * @param false $forceSyncMode
     * @return false
     */
    public function aroundSend(
        InvoiceSender $subject,
        callable $proceed,
        Invoice $invoice,
        $forceSyncMode = false
    ) {
        $order = $invoice->getOrder();
        if (!!$order->getPpIsMainOrder()) {
            return false;
        }

        $payPalMainOrderId = $order->getppParentOrderId();

        if ($payPalMainOrderId) {
            $payPalMainOrder = $this->helperData->getPaypalMainOrder($payPalMainOrderId);
            if ($payPalMainOrder) {
                $invoice->getOrder()->setPayment($payPalMainOrder->getPayment());
            }
        }

        return $proceed($invoice, $forceSyncMode);
    }
}
