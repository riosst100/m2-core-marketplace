<?php

namespace Lofmp\SplitOrderPaypal\Plugin\Model;

use Magento\InventoryShipping\Model\GetItemsToDeductFromShipment;
use Magento\Sales\Model\Order\Shipment;

class GetItemsToDeductFromShipmentPlugin
{
    /**
     * @param GetItemsToDeductFromShipment $subject
     * @param callable $proceed
     * @param Shipment $shipment
     * @return array
     */
    public function aroundExecute(
        GetItemsToDeductFromShipment $subject,
        callable $proceed,
        Shipment $shipment
    ) {
        if ($shipment->getOrder()->getPpIsMainOrder()) {
            return [];
        }
        return $proceed($shipment);
    }
}
