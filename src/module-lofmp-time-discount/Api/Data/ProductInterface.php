<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\TimeDiscount\Api\Data;

interface ProductInterface
{

    const CUSTOMER_ID = 'customer_id';
    const DATA = 'data';
    const TIME_PRODUCT_DATA = 'time_product_data';
    const TIME_DISCOUNT_PARSED = 'time_discount_parsed';
    const SELLER_ID = 'seller_id';
    const PRODUCT_ID = 'product_id';
    const ID = 'id';
    const CREATED_AT = 'created_at';
    const SORT_ORDER = 'sort_order';
    const TIME_DISCOUNT = 'time_discount';

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * Get data
     * @return string|null
     */
    public function getTimeProductData();

    /**
     * Set data
     * @param string $data
     * @return $this
     */
    public function setTimeProductData($data);

    /**
     * Get data
     * @return string|mixed|null
     */
    public function getTimeDiscountParsed();

    /**
     * Set data
     * @param string|mixed|array $data
     * @return $this
     */
    public function setTimeDiscountParsed($timeDiscountParsed);

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
}

