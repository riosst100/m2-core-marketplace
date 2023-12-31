<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\DeliverySlot\Api\Data;

interface SellerDeliverySlotInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const DATE = 'date';
    const SELLER_NAME = 'seller_name';
    const SLOTS = 'slots';

    /**
     * Get date
     * @return string|null
     */
    public function getDate();

    /**
     * Set date
     * @param string $date
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface
     */
    public function setDate($date);

    /**
     * Get seller_name
     * @return string|null
     */
    public function getSellerName();

    /**
     * Set seller_name
     * @param string $seller_name
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface
     */
    public function setSellerName($seller_name); 

    /**
     * Get slots
     * @return \Lofmp\DeliverySlot\Api\Data\SlotInterface[]|null
     */
    public function getSlots();

    /**
     * Set slots
     * @param string $slot
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface
     */
    public function setSlots($slots);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotExtensionInterface $extensionAttributes
    );
}

