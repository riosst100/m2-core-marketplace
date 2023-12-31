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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\WithdrawalInterface;
use Magento\Framework\Model\AbstractModel;

class Withdrawal extends AbstractModel implements WithdrawalInterface
{

    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Withdrawal::class);
    }

    /**
     * @inheritDoc
     */
    public function getWithdrawalId()
    {
        return $this->getData(self::WITHDRAWAL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWithdrawalId($withdrawalId)
    {
        return $this->setData(self::WITHDRAWAL_ID, $withdrawalId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function getPaymentId()
    {
        return $this->getData(self::PAYMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setPaymentId($paymentId)
    {
        return $this->setData(self::PAYMENT_ID, $paymentId);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getFee()
    {
        return $this->getData(self::FEE);
    }

    /**
     * @inheritDoc
     */
    public function setFee($fee)
    {
        return $this->setData(self::FEE, $fee);
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritDoc
     */
    public function getNetAmount()
    {
        return $this->getData(self::NET_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setNetAmount($netAmount)
    {
        return $this->setData(self::NET_AMOUNT, $netAmount);
    }

    /**
     * @inheritDoc
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * @inheritDoc
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * @inheritDoc
     */
    public function getAdminMessage()
    {
        return $this->getData(self::ADMIN_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setAdminMessage($adminMessage)
    {
        return $this->setData(self::ADMIN_MESSAGE, $adminMessage);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
