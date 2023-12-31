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

interface AmountTransactionInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const TRANSACTION_ID = 'transaction_id';
    const CUSTOMER_ID = 'customer_id';
    const SELLER_ID = 'seller_id';
    const TYPE = 'type';
    const AMOUNT = 'amount';
    const BALANCE = 'balance';
    const DESCRIPTION = 'description';
    const ADDITIONAL_INFO = 'additional_info';
    const CREATED_AT = 'created_at';

    /**
     * Get transaction_id
     *
     * @return int|null
     */
    public function getTransactionId();

    /**
     * Set transaction_id
     * @param int $transaction_id
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setTransactionId($transaction_id);

    /**
     * Get transaction_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     *
     * @param int $seller_id
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setSellerId($seller_id);

    /**
     * Get type
     * @return string|null
     */
    public function getType();

    /**
     * Set type
     * @param string $type
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setType($type);

    /**
     * Get amount
     * @return float|int|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param float|int $amount
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setAmount($amount);

    /**
     * Get balance
     * @return float|in|null
     */
    public function getBalance();

    /**
     * Set balance
     * @param float|int $amount
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setBalance($balance);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setDescription($description);

    /**
     * Get additional_info
     * @return string|null
     */
    public function getAdditionalInfo();

    /**
     * Set additional_info
     * @param string $additional_info
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
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
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     */
    public function setCreatedAt($created_at);
}
