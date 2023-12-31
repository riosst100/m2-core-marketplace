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
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class SaveSlot
 * @package Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots
 */
class SaveSlot
{
    /**
     * SaveSlot constructor.
     * @param DeliverySlotsFactory $deliverySlotsFactory
     * @param DeliverySlotsModel $deliverySlotsModel
     * @param SerializerInterface $serializer
     */
    public function __construct(
        DeliverySlotsFactory $deliverySlotsFactory,
        DeliverySlotsModel $deliverySlotsModel,
        SerializerInterface $serializer
    ) {
        $this->deliverySlotsFactory = $deliverySlotsFactory;
        $this->deliverySlotsModel = $deliverySlotsModel;
        $this->serializer = $serializer;
    }

    /**
     * @param $slotValues
     * @return bool
     */
    public function save($slotValues)
    {
        $slotId = [];
        if (array_key_exists('slot_id', $slotValues)) {
            $slotId = ['slot_id' => $slotValues['slot_id']];
        }
        try {
            $slotData = [
                'parent_id' => $slotValues['parent_id'],
                'day' => $slotValues['day'],
                'start_time' => $slotValues['start_time'],
                'end_time' => $slotValues['end_time'],
                'status' => $slotValues['status'],
                'allocation' => $slotValues['allocation']
            ];
            if (isset($slotValues['seller_id']) && $slotValues['seller_id']) {
                $slotData['seller_id'] = (int)$slotValues['seller_id'];
            }
            $slotData = array_merge($slotData, $slotId);
            $slotModel = $this->deliverySlotsModel->create(['data' => $slotData]);
            $deliverySlotResource = $this->deliverySlotsFactory->create();
            $deliverySlotResource->save($slotModel);
        } catch (\Exception $e) {
            return false;
        }
        return $slotModel;
    }
}
