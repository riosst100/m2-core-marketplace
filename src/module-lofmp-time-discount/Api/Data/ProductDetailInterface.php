<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\TimeDiscount\Api\Data;

interface ProductDetailInterface
{

    /**
     * Get time_discount
     * @return \Lofmp\TimeDiscount\Api\Data\TimeDiscountInterface[]|null
     */
    public function getTimeDiscount();

    /**
     * Set time_discount
     * @param \Lofmp\TimeDiscount\Api\Data\TimeDiscountInterface[] $time_discount
     * @return $this
     */
    public function setTimeDiscount($time_discount);

    /**
     * Get sort_order
     * @return string|null
     */
    public function getSortOrder();

    /**
     * Set sort_order
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}

