<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\TimeDiscount\Api\Data;

interface TimeDiscountInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const START = 'start';
    const END = 'end';
    const TYPE = 'type';
    const DISCOUNT = 'discount';
    const ORDER = 'order';

    /**
     * Get start
     * @return string|null
     */
    public function getStart();

    /**
     * Set start
     * @param string $start
     * @return $this
     */
    public function setStart($start);

    /**
     * Get end
     * @return string|null
     */
    public function getEnd();

    /**
     * Set end
     * @param string $end
     * @return $this
     */
    public function setEnd($end);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get discount
     * @return float|int|null
     */
    public function getDiscount();

    /**
     * Set discount
     * @param float|int $discount
     * @return $this
     */
    public function setDiscount($discount);

    /**
     * Get order
     * @return int|null
     */
    public function getOrder();

    /**
     * Set order
     * @param int $order
     * @return $this
     */
    public function setOrder($order);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lofmp\TimeDiscount\Api\Data\TimeDiscountExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lofmp\TimeDiscount\Api\Data\TimeDiscountExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lofmp\TimeDiscount\Api\Data\TimeDiscountExtensionInterface $extensionAttributes
    );
}
