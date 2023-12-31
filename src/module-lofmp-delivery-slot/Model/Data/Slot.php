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

namespace Lofmp\DeliverySlot\Model\Data;

use Lofmp\DeliverySlot\Api\Data\SlotInterface;

class Slot extends \Magento\Framework\Api\AbstractExtensibleObject implements SlotInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($seller_id)
    {
        return $this->setData(self::ID, $seller_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSlotId()
    {
        return $this->_get(self::SLOT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSlotId($slot_id)
    {
        return $this->setData(self::SLOT_ID, $slot_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartTime() 
    {
        return $this->_get(self::START_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartTime($start_time)
    {
        return $this->setData(self::START_TIME, $start_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndTime()
    {
        return $this->_get(self::END_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndTime($end_time)
    {
        return $this->setData(self::END_TIME, $end_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId()
    {
        return $this->_get(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($parent_id)
    {
        return $this->setData(self::PARENT_ID, $parent_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getDay()
    {
        return $this->_get(self::DAY);
    }

    /**
     * {@inheritdoc}
     */
    public function setDay($day)
    {
        return $this->setData(self::DAY, $day);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllocation()
    {
        return $this->_get(self::ALLOCATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllocation($allocation)
    {
        return $this->setData(self::ALLOCATION, $allocation);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentStatus()
    {
        return $this->_get(self::CURRENT_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentStatus($current_status)
    {
        return $this->setData(self::CURRENT_STATUS, $current_status);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lofmp\DeliverySlot\Api\Data\SlotExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

