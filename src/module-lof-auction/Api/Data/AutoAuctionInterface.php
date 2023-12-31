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
 * Interface AutoAuctionInterface
 * @package Lof\Auction\Api\Data
 */
interface AutoAuctionInterface
{
    const ENTITY_ID = 'entity_id';
    const AUCTION_ID = 'auction_id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const AMOUNT = 'amount';
    const WINNING_PRICE = 'winning_price';
    const SHOP = 'shop';
    const STATUS = 'status';
    const FLAG = 'flag';
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

    /**
     * Set winning_price
     *
     * @return float|null
     */
    public function getWinningPrice();

    /**
     * Set winning_price
     *
     * @param float|null
     * @return $this
     */
    public function setWinningPrice($winning_price);


    /**
     * @param $shop
     * @return mixed
     */
    public function setShop($shop);


    /**
     * @return mixed
     */
    public function getShop();

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
     * Set flag
     *
     * @return int|null
     */
    public function getFlag();

    /**
     * Set flag
     *
     * @param int|null
     * @return $this
     */
    public function setFlag($flag);

    /**
     * to Array
     *
     * @return mixed|array
     */
    public function getDataArray();
}
