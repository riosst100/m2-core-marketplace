<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots;

use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotsFactory;
use Lofmp\DeliverySlot\Model\DeliverySlotsFactory as DeliverySlotsModel;

/**
 * Class DeleteSlot
 * @package Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots
 */
class DeleteSlot
{
    /**
     * @var DeliverySlotsFactory
     */
    protected $deliverySlotsFactory;
    /**
     * @var DeliverySlotsModel
     */
    protected $deliverySlotsModel;
    /**
     * DeleteSlot constructor.
     * @param DeliverySlotsFactory $deliverySlotsFactory
     * @param DeliverySlotsModel $deliverySlotsModel
     */
    public function __construct(
        DeliverySlotsFactory $deliverySlotsFactory,
        DeliverySlotsModel $deliverySlotsModel
    ) {
        $this->deliverySlotsFactory = $deliverySlotsFactory;
        $this->deliverySlotsModel = $deliverySlotsModel;
    }

    /**
     * @param int $slotValues
     * @param int $seller_id
     * @return bool
     */
    public function delete($slotId, $seller_id = 0)
    {
        try {
            $slotData = [
                'slot_id' => $slotId,
                'seller_id' => (int)$seller_id
            ];
            $slotModel = $this->deliverySlotsModel->create(['data' => $slotData]);
            $deliverySlotResource = $this->deliverySlotsFactory->create();
            $deliverySlotResource->delete($slotModel);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
}
