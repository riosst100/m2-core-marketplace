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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Api\Data;

/**
 * Interface MixBidInterface
 * @package Lof\Auction\Api\Data
 */
interface MixBidInterface
{
    const ENTITY_ID = 'entity_id';
    const AUCTION_ID = 'auction_id';
    const CUSTOMER_ID = 'customer_id';
    const CREATED_AT = 'created_at';
    const IS_SPAM = 'is_spam';
    const PRODUCT_ID = 'product_id';
    const WINNING_STATUS = 'winning_status';
    const BID_ID = 'bid_id';
    const IS_AUTO = 'is_auto';
    const UPDATED_AT = 'updated_at';
    const RESTRICT_COUNT = 'restrict_count';
    const IS_SUBSCRIBED = 'is_subscribed';
    const CUSTOMER_NAME = 'customer_name';
    const UTC_CREATED_AT = 'utc_created_at';
    const AMOUNT = 'amount';

    /**
     * Get entity_id
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param int|null
     * @return $this
     */
    public function setEntityId($entity_id);

    /**
     * Set auction_id
     *
     * @return int|null
     */
    public function getAuctionId();

    /**
     * Set auction_id
     *
     * @param int|null
     * @return $this
     */
    public function setAuctionId($auction_id);

    /**
     * Set customer_id
     *
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param string|null
     * @return $this
     */
    public function setCustomerId($customer_id);

    /**
     * Set created_at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string|null
     * @return $this
     */
    public function setCreatedAt($created_at);

    /**
     * Set is_spam
     *
     * @return int|null
     */
    public function getIsSpam();

    /**
     * Set is_spam
     *
     * @param int|null
     * @return $this
     */
    public function setIsSpam($is_spam);

    /**
     * Set product_id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     *
     * @param int|null
     * @return $this
     */
    public function setProductId($product_id);

    /**
     * Set winning_status
     *
     * @return int|null
     */
    public function getWinningStatus();

    /**
     * Set winning_status
     *
     * @param int|null
     * @return $this
     */
    public function setWinningStatus($winning_status);

    /**
     * Set bid_id
     *
     * @return int|null
     */
    public function getBidId();

    /**
     * Set bid_id
     *
     * @param int|null
     * @return $this
     */
    public function setBidId($bid_id);

    /**
     * Set is_auto
     *
     * @return int|null
     */
    public function getIsAuto();

    /**
     * Set is_auto
     *
     * @param int|null
     * @return $this
     */
    public function setIsAuto($is_auto);

    /**
     * Set updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param string|null
     * @return $this
     */
    public function setUpdatedAt($updated_at);

    /**
     * Set restrict_count
     *
     * @return int|null
     */
    public function getRestrictCount();

    /**
     * Set restrict_count
     *
     * @param int|null
     * @return $this
     */
    public function setRestrictCount($restrict_count);

    /**
     * Set is_subscribed
     *
     * @return int|null
     */
    public function getIsSubscribed();

    /**
     * Set is_subscribed
     *
     * @param int|null
     * @return $this
     */
    public function setIsSubscribed($is_subscribed);

    /**
     * Set customer_name
     *
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer_name
     *
     * @param string|null
     * @return $this
     */
    public function setCustomerName($customer_name);

    /**
     * Set utc_created_at
     *
     * @return string|null
     */
    public function getUtcCreatedAt();

    /**
     * Set utc_created_at
     *
     * @param string|null
     * @return $this
     */
    public function setUtcCreatedAt($utc_created_at);

    /**
     * Set amount
     *
     * @return float|null
     */
    public function getAmount();

    /**
     * Set amount
     *
     * @param float|null
     * @return $this
     */
    public function setAmount($amount);

}
