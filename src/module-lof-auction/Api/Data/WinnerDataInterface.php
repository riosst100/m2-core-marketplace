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
 * Interface WinnerDataInterface
 * @package Lof\Auction\Api\Data
 */
interface WinnerDataInterface
{
    const ENTITY_ID = 'entity_id';
    const AUCTION_ID = 'auction_id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const WIN_AMOUNT = 'win_amount';
    const DAYS = 'days';
    const MIN_QTY = 'min_qty';
    const MAX_QTY = 'max_qty';
    const START_AUCTION_TIME = 'start_auction_time';
    const STOP_AUCTION_TIME = 'stop_auction_time';
    const STATUS = 'status';
    const COMPLETE = 'complete';

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
     * Set win_amount
     *
     * @return float|null
     */
    public function getWinAmount();

    /**
     * Set win_amount
     *
     * @param float|null
     * @return $this
     */
    public function setWinAmount($win_amount);

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
     * Set complete
     *
     * @return int|null
     */
    public function getComplete();

    /**
     * Set complete
     *
     * @param int|null
     * @return $this
     */
    public function setComplete($complete);

    /**
     * to Array
     *
     * @return mixed|array
     */
    public function getDataArray();
}
