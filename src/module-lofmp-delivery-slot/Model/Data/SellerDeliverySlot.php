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

use Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface;

class SellerDeliverySlot extends \Magento\Framework\Api\AbstractExtensibleObject implements SellerDeliverySlotInterface
{
    /**
     * Get date
     * @return string|null
     */
    public function getDate()
    {
        return $this->_get(self::DATE);
    }

    /**
     * Set date
     * @param string $date
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface
     */
    public function setDate($date)
    {
        return $this->setData(self::DATE, $date);
    }

    /**
     * Get seller_name
     * @return string|null
     */
    public function getSellerName()
    {
        return $this->_get(self::SELLER_NAME);
    }

    /**
     * Set seller_name
     * @param string $seller_name
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface
     */
    public function setSellerName($seller_name)
    {
        return $this->setData(self::SELLER_NAME, $seller_name);
    }

    /**
     * Get slots
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface[]|null
     */
    public function getSlots()
    {
        return $this->_get(self::SLOTS);
    }

    /**
     * Set slots
     * @param string $slot
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface
     */
    public function setSlots($slots)
    {
        return $this->setData(self::SLOTS, $slots);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

