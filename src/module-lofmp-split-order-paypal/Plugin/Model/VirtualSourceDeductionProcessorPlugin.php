<?php

namespace Lofmp\SplitOrderPaypal\Plugin\Model;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\InventoryShipping\Observer\VirtualSourceDeductionProcessor;

class VirtualSourceDeductionProcessorPlugin
{
    /**
     * @param VirtualSourceDeductionProcessor $subject
     * @param callable $proceed
     * @param EventObserver $observer
     * @return void
     */
    public function aroundExecute(
        VirtualSourceDeductionProcessor $subject,
        callable $proceed,
        EventObserver $observer
    ) {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        if ($invoice->getOrder()->getPpIsMainOrder()) {
            return;
        }
        return $proceed($observer);
    }
}
