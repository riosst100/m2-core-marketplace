<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\DeliverySlot\Api\Data;

interface SlotInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'seller_id';
    const SLOT_ID = 'slot_id';
    const START_TIME = 'start_time';
    const END_TIME = 'end_time';
    const PARENT_ID = 'parent_id';
    const STATUS = 'status';
    const DAY = 'day';
    const ALLOCATION = 'allocation';
    const CURRENT_STATUS = 'current_status';

    /**
     * Get seller_id
     * @return int|null
     */
    public function getId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setId($seller_id);

    /**
     * Get slot_id
     * @return int|null
     */
    public function getSlotId();

    /**
     * Set slot_id
     * @param int $slot_id
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setSlotId($slot_id);

    /**
     * Get start_time
     * @return string|null
     */
    public function getStartTime();

    /**
     * Set start_time
     * @param string $start_time
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setStartTime($start_time);

    /**
     * Get end_time
     * @return string|null
     */
    public function getEndTime();

    /**
     * Set end_time
     * @param string $end_time
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setEndTime($end_time);

    /**
     * Get parent_id
     * @return int|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param int $parent_id
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setParentId($parent_id);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setStatus($status);

    /**
     * Get status
     * @return string|null
     */
    public function getDay();

    /**
     * Set day
     * @param string $day
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setDay($day);

    /**
     * Get allocation
     * @return int|null
     */
    public function getAllocation();

    /**
     * Set allocation
     * @param int $allocation
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setAllocation($allocation);

    /**
     * Get current_status
     * @return int|null
     */
    public function getCurrentStatus();

    /**
     * Set current_status
     * @param int $current_status
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface
     */
    public function setCurrentStatus($current_status);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\DeliverySlot\Api\Data\SlotExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lofmp\DeliverySlot\Api\Data\SlotExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\DeliverySlot\Api\Data\SlotExtensionInterface $extensionAttributes
    );
}

