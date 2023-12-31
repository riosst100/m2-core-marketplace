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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Api\Data;

interface TransactionInterface
{
    const TRANSACTION_ID = 'transaction_id';
    const NAME = 'name';
    const CUSTOMER_ID = 'customer_id';
    const PACKAGE = 'package';
    const AMOUNT = 'amount';
    const DURATION = 'duration';
    const DURATION_UNIT= 'duration_unit';
    const ADDITIONAL_INFO = 'additional_info';
    const CREATED_AT = 'created_at';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_OPTIONS = 'product_options';
    const ITEM_ID = 'item_id';
    const GROUP_ID = 'group_id';

    /**
     * Get transaction_id
     * @return int|null
     */
    public function getTransactionId();

    /**
     * Set transaction_id
     * @param int $transaction_id
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setTransactionId($transaction_id);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setName($name);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get package
     * @return string|null
     */
    public function getPackage();

    /**
     * Set package
     * @param mixed $package
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setPackage($package);

    /**
     * Get amount
     * @return float|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param float $amount
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setAmount($amount);

    /**
     * Get duration
     * @return string|null
     */
    public function getDuration();

    /**
     * Set duration
     * @param string $duration
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setDuration($duration);

    /**
     * Get duration_unit
     * @return string|null
     */
    public function getDurationUnit();

    /**
     * Set duration_unit
     * @param string $duration_unit
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setDurationUnit($duration_unit);

    /**
     * Get additional_info
     * @return string|null
     */
    public function getAdditionalInfo();

    /**
     * Set additional_info
     * @param string $additional_info
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setAdditionalInfo($additional_info);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setProductId($product_id);

    /**
     * Get product_options
     * @return string|null
     */
    public function getProductOptions();

    /**
     * Set product_options
     * @param string $product_options
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setProductOptions($product_options);

    /**
     * Get item_id
     * @return int|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param int $item_id
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setItemId($item_id);

    /**
     * Get group_id
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param int $group_id
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     */
    public function setGroupId($group_id);
}
