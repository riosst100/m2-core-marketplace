<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\DeliverySlot\Api\Data;

interface SellerDeliverySlotSearchResultsInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ITEMS = 'items';
    const VACATION_MESSAGES = 'vacation_messages';
    const NO_SLOTS_MESSAGES = 'no_slots_messages';
    /**
     * Get transaction list.
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface[]
     */
    public function getItems();

    /**
     * Set staff list.
     * @param \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface[] $items
     * @return $this
     */
    public function setItems( $items);

    /**
     * get vacation messages list.
     * @return string[]|string|null $message
     */
    public function getVacationMessages();

    /**
     * Set vacation messages list.
     * @param string[]|string|null $message
     * @return $this
     */
    public function setVacationMessages( $messages );

    /**
     * Get no slots messages list.
     * @return string[]|string|null $message
     */
    public function getNoSlotsMessages();

    /**
     * Set no slots messages list.
     * @param string[]|string|null $message
     * @return $this
     */
    public function setNoSlotsMessages( $messages );
}
