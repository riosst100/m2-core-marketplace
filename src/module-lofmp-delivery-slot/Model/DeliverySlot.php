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

namespace Lofmp\DeliverySlot\Model;

use Lofmp\DeliverySlot\Api\DeliverySlotInterface;
use Lofmp\DeliverySlot\Helper\Data;
use Magento\Framework\Serialize\SerializerInterface;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\CollectionFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup\CollectionFactory as DeliverSlotGroupCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;

/**
 * Class DeliverySlot
 * @package Lofmp\DeliverySlot\Model
 */
class DeliverySlot implements DeliverySlotInterface
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DeliverSlotGroupCollection
     */
    protected $deliverSlotGroupCollection;

    /**
     * DeliverySlot constructor.
     *
     * @param Data $data
     * @param SerializerInterface $serializer
     * @param CollectionFactory $collectionFactory
     * @param DeliverSlotGroupCollection $deliverSlotGroupCollection
     * @param DateTime $dateTime
     * @param OrderCollection $orderCollection
     */
    public function __construct(
        Data $data,
        SerializerInterface $serializer,
        CollectionFactory $collectionFactory,
        DeliverSlotGroupCollection $deliverSlotGroupCollection,
        DateTime $dateTime,
        OrderCollection $orderCollection
    ) {
    
        $this->data = $data;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
        $this->deliverSlotGroupCollection = $deliverSlotGroupCollection;
        $this->dateTime = $dateTime;
        $this->orderCollection = $orderCollection;
    }

    /**
     * @return string
     */
    public function getConfig($zip_code, $target_date = '')
    {
        if ($this->data->getDeliverySlotConfig('enabled') != 0) {
            if ($this->data->checkVacationMode($target_date)==1) {
                $deliverySlotGroups = $this->deliverSlotGroupCollection->create()
                    ->addFieldToFilter('zip_code', ['like' => "%$zip_code%"])
                    ->addFieldToFilter('seller_id', 0);
                $slotGroupId = '';
                foreach ($deliverySlotGroups as $deliverySlotGroup) {
                    $zipCodes = $deliverySlotGroup->getZipCode();
                    $zipCodes = explode(",", $zipCodes);
                    if (in_array($zip_code, $zipCodes)) {
                        $slotGroupId = $deliverySlotGroup->getId();
                    }
                }
                if (!empty($slotGroupId) && isset($slotGroupId)) {
                    $currentDate = $this->dateTime->gmtDate();
                    $dates = [];
                    for ($i = 0; $i < $this->data->getDeliverySlotConfig('no_of_days'); $i++) {
                        $dates[] = $this->dateTime->date('D, d M Y', strtotime("+$i day", strtotime($currentDate)));
                    }
                    $slots = [];
                    foreach ($dates as $date) {
                        $day = strtolower($this->dateTime->date('D', $date));
                        $deliverySlotCollection = $this->collectionFactory->create()
                            ->addFieldToFilter('day', $day)
                            ->addFieldToFilter('start_time', ['gteq' => $this->dateTime->date(' H:i')])
                            ->addFieldToFilter('status', 1)
                            ->addFieldToFilter('parent_id', $slotGroupId)
                            ->addFieldToFilter('seller_id', 0);
                        $deliverySlot = $deliverySlotCollection->getData();
                        $slots[$date] = $deliverySlot;
                    }
                    $finalAvailableSlots = [];
                    foreach ($slots as $key => $slot) {
                        $day = strtolower($this->dateTime->date('D', $key));
                        $availableSlots = [];
                        foreach ($slot as $sl) {
                            if ($sl['day'] == $day) {
                                $date = $this->dateTime->date('Y-m-d', $key);
                                $orderCollection = $this->orderCollection->create();
                                $ordersCount = $orderCollection->addFieldToFilter('delivery_date', $date)->getTotalCount();
                                if (!$sl['allocation'] || $ordersCount < $sl['allocation']) {
                                    $sl['current_status'] = 1;
                                    $availableSlots[] = $sl;
                                } else {
                                    $sl['current_status'] = 0;
                                    $availableSlots[] = $sl;
                                }
                            }
                        }
                        if (!empty($availableSlots)) {
                            $finalAvailableSlots[] = [
                                'date' => $key,
                                'slots' => $availableSlots
                            ];
                        }
                    }
                    return $finalAvailableSlots;
                } else {
                    return "No Slots Available For Following ZipCode: $zip_code";
                }
            } else {
                $vacationMessage = $this->data->getDeliverySlotVacationConfig('message');
                $from_date = $this->data->getDeliverySlotVacationConfig('from_date');
                $to_date = $this->data->getDeliverySlotVacationConfig('to_date');

                if (isset($vacationMessage) && !empty($vacationMessage)) {
                    return $vacationMessage." from ".$from_date." to ".$to_date;
                } else {
                    return "Slots not available , Vacation Period from ".$from_date." to ".$to_date;
                }
            }
        }

        return "Delivery Slot Is Not Enabled";
    }
}
