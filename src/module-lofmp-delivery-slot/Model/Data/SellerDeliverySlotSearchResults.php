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

use Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotSearchResultsInterface;

class SellerDeliverySlotSearchResults extends \Magento\Framework\Api\AbstractExtensibleObject implements SellerDeliverySlotSearchResultsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->_get(self::ITEMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setItems($items)
    {
        return $this->setData(self::ITEMS, $items);
    }

    /**
     * {@inheritdoc}
     */
    public function getVacationMessages()
    {
        return $this->_get(self::VACATION_MESSAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setVacationMessages( $messages )
    {
        return $this->setData(self::VACATION_MESSAGES, $messages);
    }

    /**
     * {@inheritdoc}
     */
    public function getNoSlotsMessages()
    {
        return $this->_get(self::NO_SLOTS_MESSAGES);
    }

    /**
     * {@inheritdoc}
     */
    public function setNoSlotsMessages( $messages )
    {
        return $this->setData(self::NO_SLOTS_MESSAGES, $messages);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotSearchResultsExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotSearchResultsExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotSearchResultsExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

