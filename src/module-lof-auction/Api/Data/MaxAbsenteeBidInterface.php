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
 * Interface MaxAbsenteeBidInterface
 * @package Lof\Auction\Api\Data
 */
interface MaxAbsenteeBidInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const AUCTION_ID = 'auction_id';
    const PRODUCT_ID = 'product_id';
    const CUSTOMER_ID = 'customer_id';
    const MAX_ABSENTEE_AMOUNT = 'max_absentee_amount';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

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
     * Set max_absentee_amount
     *
     * @return float|null
     */
    public function getMaxAbsenteeAmount();

    /**
     * Set max_absentee_amount
     *
     * @param float|null
     * @return $this
     */
    public function setMaxAbsenteeAmount($max_absentee_amount);

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
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Auction\Api\Data\MaxAbsenteeBidExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Auction\Api\Data\MaxAbsenteeBidExtensionInterface $extensionAttributes
    );
}
