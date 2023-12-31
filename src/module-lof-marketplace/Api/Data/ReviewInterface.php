<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Api\Data;

interface ReviewInterface
{
//    const ID = 'id';
    const REVIEWSELLER_ID = 'reviewseller_id';
    const TYPE = 'type';
    const SELLER_ID = 'seller_id';
    const CUSTOMER_ID = 'customer_id';
    const REVIEW_ID = 'review_id';
    const PRODUCT_ID = 'product_id';
    const ORDER_ID = 'order_id';
    const IS_PUBLIC = 'is_public';
    const RATING = 'rating';
    const STATUS = 'status';
    const TITLE = 'title';
    const DETAIL = 'detail';
    const NICKNAME = 'nickname';
    const CREATED_AT = 'created_at';

    /**
     * Get reviewseller_id
     * @return int|null
     */
    public function getReviewsellerId();

    /**
     * Set reviewseller_id
     * @param int $reviewseller_id
     * @return $this
     */
    public function setReviewsellerId($reviewseller_id);

    /**
     * Get type
     * @return int|null
     */
    public function getType();

    /**
     * Set type
     * @param int $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);

    /**
     * Get review_id
     * @return int|null
     */
    public function getReviewId();

    /**
     * Set review_id
     * @param int $review_id
     * @return $this
     */
    public function setReviewId($review_id);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return $this
     */
    public function setProductId($product_id);

    /**
     * Get order_id
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param int $order_id
     * @return $this
     */
    public function setOrderId($order_id);

    /**
     * Get is_public
     * @return int|null
     */
    public function getIsPublic();

    /**
     * Set is_public
     * @param int $is_public
     * @return $this
     */
    public function setIsPublic($is_public);

    /**
     * Get rating
     * @return int|null
     */
    public function getRating();

    /**
     * Set rating
     * @param int $rating
     * @return $this
     */
    public function setRating($rating);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get title
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get detail
     * @return string|null
     */
    public function getDetail();

    /**
     * Set detail
     * @param string $detail
     * @return $this
     */
    public function setDetail($detail);

    /**
     * Get nickname
     * @return string|null
     */
    public function getNickname();

    /**
     * Set nickname
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);
}
