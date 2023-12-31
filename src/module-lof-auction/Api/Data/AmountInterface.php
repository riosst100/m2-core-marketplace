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
 * Interface AmountInterface
 * @package Lof\Auction\Api\Data
 */
interface AmountInterface
{
    const ENTITY_ID = 'entity_id';
    const AUCTION_ID = 'auction_id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const AUCTION_AMOUNT = 'auction_amount';
    const WINNING_STATUS = 'winning_status';
    const SHOP = 'shop';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';

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
     * Set auction_amount
     *
     * @return float|null
     */
    public function getAuctionAmount();

    /**
     * Set auction_amount
     *
     * @param float|null
     * @return $this
     */
    public function setAuctionAmount($auction_amount);

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
     * Set shop
     *
     * @return int|null
     */
    public function getShop();

    /**
     * Set shop
     *
     * @param int|null
     * @return $this
     */
    public function setShop($shop);

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
     * to Array
     *
     * @return mixed|array
     */
    public function getDataArray();
}
