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
 * Interface ProductInterface
 * @package Lof\Auction\Api\Data
 */
interface ProductInterface
{
    const ENTITY_ID = 'entity_id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const STARTING_PRICE = 'starting_price';
    const RESERVE_PRICE = 'reserve_price';
    const AUCTION_STATUS = 'auction_status';
    const DAYS = 'days';
    const MIN_QTY = 'min_qty';
    const MAX_QTY = 'max_qty';
    const MIN_AMOUNT = 'min_amount';
    const MAX_AMOUNT = 'max_amount';
    const START_AUCTION_TIME = 'start_auction_time';
    const STOP_AUCTION_TIME = 'stop_auction_time';
    const INCREMENT_OPT = 'increment_opt';
    const INCREMENT_PRICE = 'increment_price';
    const AUTO_AUCTION_OPT = 'auto_auction_opt';
    const STATUS = 'status';
    const FEATURED_AUCTION = 'featured_auction';
    const BUY_IT_NOW = 'buy_it_now';
    const CREATED_AT = 'created_at';

    const CURRENT_TIME_STAMP = 'current_time_stamp';
    const CONTINUE_TIME = 'continue_time';
    const LIMIT_BIDS = 'limit_bids';
    const SELLER_ID = 'seller_id';
    const TIME_ZONE = 'time_zone';
    const STOP_AUCTION_UTC_TIME = 'stop_auction_utc_time';
    const START_AUCTION_UTC_TIME = 'start_auction_utc_time';
    const TODAY_TIME = 'today_time';
    const START_AUCTION_TIME_STAMP = 'start_auction_time_stamp';
    const STOP_AUCTION_TIME_STAMP = 'stop_auction_time_stamp';
    const NEW_AUCTION_START = 'new_auction_start';
    const PRO_URL = 'pro_url';
    const PRO_NAME = 'pro_name';
    const CUSTOMER_NAME = 'customer_name';
    const MAX_BIDS = 'max_bids';

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
     * Set status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int|null
     * @return $this
     */
    public function setStatus($status);

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
     * Set starting_price
     *
     * @return int|null
     */
    public function getStartingPrice();

    /**
     * Set starting_price
     *
     * @param int|null
     * @return $this
     */
    public function setStartingPrice($starting_price);

    /**
     * Set reserve_price
     *
     * @return float|null
     */
    public function getReservePrice();

    /**
     * Set reserve_price
     *
     * @param float|null
     * @return $this
     */
    public function setReservePrice($reserve_price);

    /**
     * Set days
     *
     * @return int|null
     */
    public function getDays();

    /**
     * Set days
     *
     * @param int|null
     * @return $this
     */
    public function setDays($days);

    /**
     * Set min_qty
     *
     * @return int|null
     */
    public function getMinQty();

    /**
     * Set min_qty
     *
     * @param int|null
     * @return $this
     */
    public function setMinQty($min_qty);

    /**
     * Set max_qty
     *
     * @return int|null
     */
    public function getMaxQty();

    /**
     * Set max_qty
     *
     * @param int|null
     * @return $this
     */
    public function setMaxQty($max_qty);

    /**
     * Set start_auction_time
     *
     * @return string|null
     */
    public function getStartAuctionTime();

    /**
     * Set start_auction_time
     *
     * @param string|null
     * @return $this
     */
    public function setStartAuctionTime($start_auction_time);

    /**
     * Set stop_auction_time
     *
     * @return string|null
     */
    public function getStopAuctionTime();

    /**
     * Set stop_auction_time
     *
     * @param string|null
     * @return $this
     */
    public function setStopAuctionTime($stop_auction_time);

    /**
     * Set auction_status
     *
     * @return int|null
     */
    public function getAuctionStatus();

    /**
     * Set auction_status
     *
     * @param int|null
     * @return $this
     */
    public function setAuctionStatus($auction_status);

    /**
     * Set increment_opt
     *
     * @return int|null
     */
    public function getIncrementOpt();

    /**
     * Set increment_opt
     *
     * @param int|null
     * @return $this
     */
    public function setIncrementOpt($increment_opt);

    /**
     * Set increment_price
     *
     * @return string|null
     */
    public function getIncrementPrice();

    /**
     * Set increment_price
     *
     * @param string|null
     * @return $this
     */
    public function setIncrementPrice($increment_price);

    /**
     * Set auto_auction_opt
     *
     * @return int|null
     */
    public function getAutoAuctionOpt();

    /**
     * Set auto_auction_opt
     *
     * @param int|null
     * @return $this
     */
    public function setAutoAuctionOpt($auto_auction_opt);

    /**
     * Set featured_auction
     *
     * @return int|null
     */
    public function getFeaturedAuction();

    /**
     * Set featured_auction
     *
     * @param int|null
     * @return $this
     */
    public function setFeaturedAuction($featured_auction);

    /**
     * Set buy_it_now
     *
     * @return int|null
     */
    public function getBuyItNow();

    /**
     * Set buy_it_now
     *
     * @param int|null
     * @return $this
     */
    public function setBuyItNow($buy_it_now);

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
     * Set max_amount
     *
     * @return float|int|null
     */
    public function getMaxAmount();

    /**
     * Set max_amount
     *
     * @param float|int|null
     * @return $this
     */
    public function setMaxAmount($max_amount);

    /**
     * Set min_amount
     *
     * @return float|int|null
     */
    public function getMinAmount();

    /**
     * Set min_amount
     *
     * @param float|int|null
     * @return $this
     */
    public function setMinAmount($min_amount);

    /**
     * to Array
     *
     * @return mixed|array
     */
    public function getDataArray();

    /**
     * Set current_time_stamp
     *
     * @return string
     */
    public function getCurrentTimeStamp();

    /**
     * Set current_time_stamp
     *
     * @param string
     * @return $this
     */
    public function setCurrentTimeStamp($current_time_stamp);

    /**
     * Set continue_time
     *
     * @return string
     */
    public function getContinueTime();

    /**
     * Set continue_time
     *
     * @param string
     * @return $this
     */
    public function setContinueTime($continue_time);

    /**
     * Set limit_bids
     *
     * @return int
     */
    public function getLimitBids();

    /**
     * Set limit_bids
     *
     * @param int
     * @return $this
     */
    public function setLimitBids($limit_bids);

    /**
     * Set seller_id
     *
     * @return int
     */
    public function getSellerId();

    /**
     * Set seller_id
     *
     * @param int
     * @return $this
     */
    public function setSellerId($seller_id);

    /**
     * Set time_zone
     *
     * @return string
     */
    public function getTimeZone();

    /**
     * Set time_zone
     *
     * @param string
     * @return $this
     */
    public function setTimeZone($time_zone);

    /**
     * Set stop_auction_utc_time
     *
     * @return string
     */
    public function getStopAuctionUtcTime();

    /**
     * Set stop_auction_utc_time
     *
     * @param string
     * @return $this
     */
    public function setStopAuctionUtcTime($stop_auction_utc_time);

    /**
     * Set start_auction_utc_time
     *
     * @return string
     */
    public function getStartAuctionUtcTime();

    /**
     * Set start_auction_utc_time
     *
     * @param string
     * @return $this
     */
    public function setStartAuctionUtcTime($start_auction_utc_time);

    /**
     * Set today_time
     *
     * @return string
     */
    public function getTodayTime();

    /**
     * Set today_time
     *
     * @param string
     * @return $this
     */
    public function setTodayTime($today_time);

    /**
     * Set start_auction_time_stamp
     *
     * @return string
     */
    public function getStartAuctionTimeStamp();

    /**
     * Set start_auction_time_stamp
     *
     * @param string
     * @return $this
     */
    public function setStartAuctionTimeStamp($start_auction_time_stamp);

    /**
     * Set stop_auction_time_stamp
     *
     * @return string
     */
    public function getStopAuctionTimeStamp();

    /**
     * Set stop_auction_time_stamp
     *
     * @param string
     * @return $this
     */
    public function setStopAuctionTimeStamp($stop_auction_time_stamp);

    /**
     * Set new_auction_start
     *
     * @return bool
     */
    public function getNewAuctionStart();

    /**
     * Set new_auction_start
     *
     * @param bool
     * @return $this
     */
    public function setNewAuctionStart($new_auction_start);

    /**
     * Set pro_url
     *
     * @return string
     */
    public function getProUrl();

    /**
     * Set pro_url
     *
     * @param string
     * @return $this
     */
    public function setProUrl($pro_url);

    /**
     * Set pro_name
     *
     * @return string
     */
    public function getProName();

    /**
     * Set pro_name
     *
     * @param string
     * @return $this
     */
    public function setProName($pro_name);

    /**
     * Set customer_name
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set customer_name
     *
     * @param string
     * @return $this
     */
    public function setCustomerName($customer_name);

    /**
     * Set max_bids
     *
     * @return int
     */
    public function getMaxBids();

    /**
     * Set max_bids
     *
     * @param int
     * @return $this
     */
    public function setMaxBids($max_bids);
}
