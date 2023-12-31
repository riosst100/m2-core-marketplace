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

use Lof\MarketPlace\Api\Data\PaymentInterface;
use Magento\Framework\Model\AbstractModel;

class Payment extends AbstractModel implements PaymentInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_payment';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'payment';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Payment::class);
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
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
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
    public function getMinAmount()
    {
        return $this->getData(self::MIN_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setMinAmount($minAmount)
    {
        return $this->setData(self::MIN_AMOUNT, $minAmount);
    }

    /**
     * @inheritDoc
     */
    public function getMaxAmount()
    {
        return $this->getData(self::MAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setMaxAmount($maxAmount)
    {
        return $this->setData(self::MAX_AMOUNT, $maxAmount);
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return $this->getData(self::ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setOrder($order)
    {
        return $this->setData(self::ORDER, $order);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritDoc
     */
    public function getEmailAccount()
    {
        return $this->getData(self::EMAIL_ACCOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setEmailAccount($emailAccount)
    {
        return $this->setData(self::EMAIL_ACCOUNT, $emailAccount);
    }

    /**
     * @inheritDoc
     */
    public function getAdditional()
    {
        return $this->getData(self::ADDITIONAL);
    }

    /**
     * @inheritDoc
     */
    public function setAdditional($additional)
    {
        return $this->setData(self::ADDITIONAL, $additional);
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

    /**
     * @inheritDoc
     */
    public function getFeeBy()
    {
        return $this->getData(self::FEE_BY);
    }

    /**
     * @inheritDoc
     */
    public function setFeeBy($feeBy)
    {
        return $this->setData(self::FEE_BY, $feeBy);
    }

    /**
     * @inheritDoc
     */
    public function getFeePercent()
    {
        return $this->getData(self::FEE_PERCENT);
    }

    /**
     * @inheritDoc
     */
    public function setFeePercent($feePercent)
    {
        return $this->setData(self::FEE_PERCENT, $feePercent);
    }
}
