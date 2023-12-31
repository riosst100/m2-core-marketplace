<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface PaymentInterface
{

    const MESSAGE = 'message';
    const STATUS = 'status';
    const NAME = 'name';
    const FEE_BY = 'fee_by';
    const FEE_PERCENT = 'fee_percent';
    const UPDATED_AT = 'updated_at';
    const MIN_AMOUNT = 'min_amount';
    const DESCRIPTION = 'description';
    const ORDER = 'order';
    const ADDITIONAL = 'additional';
    const MAX_AMOUNT = 'max_amount';
    const EMAIL_ACCOUNT = 'email_account';
    const CREATED_AT = 'created_at';
    const PAYMENT_ID = 'payment_id';
    const FEE = 'fee';

    /**
     * Get payment_id
     * @return string|null
     */
    public function getPaymentId();

    /**
     * Set payment_id
     * @param string $paymentId
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setPaymentId($paymentId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setName($name);

    /**
     * Get fee
     * @return string|null
     */
    public function getFee();

    /**
     * Set fee
     * @param string $fee
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setFee($fee);

    /**
     * Get min_amount
     * @return string|null
     */
    public function getMinAmount();

    /**
     * Set min_amount
     * @param string $minAmount
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setMinAmount($minAmount);

    /**
     * Get max_amount
     * @return string|null
     */
    public function getMaxAmount();

    /**
     * Set max_amount
     * @param string $maxAmount
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setMaxAmount($maxAmount);

    /**
     * Get order
     * @return string|null
     */
    public function getOrder();

    /**
     * Set order
     * @param string $order
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setOrder($order);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setDescription($description);

    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setMessage($message);

    /**
     * Get email_account
     * @return string|null
     */
    public function getEmailAccount();

    /**
     * Set email_account
     * @param string $emailAccount
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setEmailAccount($emailAccount);

    /**
     * Get additional
     * @return string|null
     */
    public function getAdditional();

    /**
     * Set additional
     * @param string $additional
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setAdditional($additional);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get fee_by
     * @return string|null
     */
    public function getFeeBy();

    /**
     * Set fee_by
     * @param string $feeBy
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setFeeBy($feeBy);

    /**
     * Get fee_percent
     * @return string|null
     */
    public function getFeePercent();

    /**
     * Set fee_percent
     * @param string $feePercent
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     */
    public function setFeePercent($feePercent);
}

