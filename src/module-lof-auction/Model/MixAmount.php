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


namespace Lof\Auction\Model;

use Lof\Auction\Api\Data\MixBidInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class MixAmount
 * @package Lof\Auction\Model
 */
class MixAmount extends \Magento\Framework\Model\AbstractModel implements MixBidInterface
{
    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        parent::__construct($context, $registry);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Lof\Auction\Model\ResourceModel\MixAmount');
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     * @inheritdoc
     */
    public function getAuctionId()
    {
        return $this->getData(self::AUCTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setAuctionId($auction_id)
    {
        return $this->setData(self::AUCTION_ID, $auction_id);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * @inheritdoc
     */
    public function getIsSpam()
    {
        return $this->getData(self::IS_SPAM);
    }

    /**
     * @inheritdoc
     */
    public function setIsSpam($is_spam)
    {
        return $this->setData(self::IS_SPAM, $is_spam);
    }

    /**
     * @inheritdoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * @inheritdoc
     */
    public function getWinningStatus()
    {
        return $this->getData(self::WINNING_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setWinningStatus($winning_status)
    {
        return $this->setData(self::WINNING_STATUS, $winning_status);
    }

    /**
     * @inheritdoc
     */
    public function getBidId()
    {
        return $this->getData(self::BID_ID);
    }

    /**
     * @inheritdoc
     */
    public function setBidId($bid_id)
    {
        return $this->setData(self::BID_ID, $bid_id);
    }

    /**
     * @inheritdoc
     */
    public function getIsAuto()
    {
        return $this->getData(self::IS_AUTO);
    }

    /**
     * @inheritdoc
     */
    public function setIsAuto($is_auto)
    {
        return $this->setData(self::IS_AUTO, $is_auto);
    }

    /**
     * @inheritdoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }

    /**
     * @inheritdoc
     */
    public function getRestrictCount()
    {
        return $this->getData(self::RESTRICT_COUNT);
    }

    /**
     * @inheritdoc
     */
    public function setRestrictCount($restrict_count)
    {
        return $this->setData(self::RESTRICT_COUNT, $restrict_count);
    }

    /**
     * @inheritdoc
     */
    public function getIsSubscribed()
    {
        return $this->getData(self::IS_SUBSCRIBED);
    }

    /**
     * @inheritdoc
     */
    public function setIsSubscribed($is_subscribed)
    {
        return $this->setData(self::IS_SUBSCRIBED, $is_subscribed);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerName($customer_name)
    {
        return $this->setData(self::CUSTOMER_NAME, $customer_name);
    }

    /**
     * @inheritdoc
     */
    public function getUtcCreatedAt()
    {
        return $this->getData(self::UTC_CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setUtcCreatedAt($utc_created_at)
    {
        return $this->setData(self::UTC_CREATED_AT, $utc_created_at);
    }
}
