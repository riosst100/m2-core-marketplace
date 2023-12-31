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

namespace Lofmp\SplitOrderPaypal\Plugin\Sales\Block\Adminhtml\Order;

use Magento\Sales\Block\Adminhtml\Order\Payment as BlockPayment;
use Lofmp\SplitOrderPaypal\Helper\Data;

class PaymentPlugin
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
     * @param BlockPayment $subject
     * @param $parentBlock
     * @return mixed
     */
    public function afterGetParentBlock(
        BlockPayment $subject,
        $parentBlock
    ) {
        $order = $parentBlock->getOrder();
        $payPalMainOrderId = $order->getppParentOrderId();

        if ($payPalMainOrderId) {
            $payPalMainOrder = $this->helperData->getPaypalMainOrder($payPalMainOrderId);
            if ($payPalMainOrder) {
                $parentBlock->getOrder()->setPayment($payPalMainOrder->getPayment());
            }
        }
        return $parentBlock;
    }
}
