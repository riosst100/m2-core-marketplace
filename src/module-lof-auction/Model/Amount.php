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

use Magento\Framework\Model\Context;
use Lof\Auction\Api\Data\AmountInterface;
use Magento\Framework\Registry;

/**
 * Class Amount
 * @package Lof\Auction\Model
 */
class Amount extends AbstractAuctionModel implements AmountInterface
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
     *
     */
    protected function _construct()
    {
        $this->_init('Lof\Auction\Model\ResourceModel\Amount');
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }
    /**
     * {@inheritdoc}
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }
    /**
     * {@inheritdoc}
     */
    public function getAuctionId()
    {
        return $this->getData(self::AUCTION_ID);
    }
    /**
     * {@inheritdoc}
     */
    public function setAuctionId($auction_id)
    {
        return $this->setData(self::AUCTION_ID, $auction_id);
    }
    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }
    /**
     * {@inheritdoc}
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }
    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }
    /**
     * {@inheritdoc}
     */
    public function getAuctionAmount()
    {
        return $this->getData(self::AUCTION_AMOUNT);
    }
    /**
     * {@inheritdoc}
     */
    public function setAuctionAmount($auction_amount)
    {
        return $this->setData(self::AUCTION_AMOUNT, $auction_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getWinningStatus()
    {
        return $this->getData(self::WINNING_STATUS);
    }
    /**
     * {@inheritdoc}
     */
    public function setWinningStatus($winning_status)
    {
        return $this->setData(self::WINNING_STATUS, $winning_status);
    }
    /**
     * {@inheritdoc}
     */
    public function setShop($shop)
    {
        return $this->setData(self::SHOP);
    }
    /**
     * {@inheritdoc}
     */
    public function getShop()
    {
        return $this->getData(self::SHOP);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * @inheritdoc
     */
    public function getDataArray()
    {
        return $this->__toArray();
    }
}
