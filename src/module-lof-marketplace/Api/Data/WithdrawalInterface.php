<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface WithdrawalInterface
{

    const EMAIL = 'email';
    const AMOUNT = 'amount';
    const COMMENT = 'comment';
    const STATUS = 'status';
    const NET_AMOUNT = 'net_amount';
    const SELLER_ID = 'seller_id';
    const ADMIN_MESSAGE = 'admin_message';
    const UPDATED_AT = 'updated_at';
    const WITHDRAWAL_ID = 'withdrawal_id';
    const CREATED_AT = 'created_at';
    const PAYMENT_ID = 'payment_id';
    const FEE = 'fee';

    /**
     * Get withdrawal_id
     * @return string|null
     */
    public function getWithdrawalId();

    /**
     * Set withdrawal_id
     * @param string $withdrawalId
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setWithdrawalId($withdrawalId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get payment_id
     * @return string|null
     */
    public function getPaymentId();

    /**
     * Set payment_id
     * @param string $paymentId
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setPaymentId($paymentId);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setEmail($email);

    /**
     * Get fee
     * @return string|null
     */
    public function getFee();

    /**
     * Set fee
     * @param string $fee
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setFee($fee);

    /**
     * Get amount
     * @return string|null
     */
    public function getAmount();

    /**
     * Set amount
     * @param string $amount
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setAmount($amount);

    /**
     * Get net_amount
     * @return string|null
     */
    public function getNetAmount();

    /**
     * Set net_amount
     * @param string $netAmount
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setNetAmount($netAmount);

    /**
     * Get comment
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     * @param string $comment
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setComment($comment);

    /**
     * Get admin_message
     * @return string|null
     */
    public function getAdminMessage();

    /**
     * Set admin_message
     * @param string $adminMessage
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setAdminMessage($adminMessage);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
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
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
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
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     */
    public function setUpdatedAt($updatedAt);
}

