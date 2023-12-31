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

use Lof\Auction\Api\Data\AutoAuctionInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class AutoAuction
 * @package Lof\Auction\Model
 */
class AutoAuction extends AbstractAuctionModel implements AutoAuctionInterface
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

        $this->_init('Lof\Auction\Model\ResourceModel\AutoAuction');
    }

    /**
     * @return array|int|mixed|null
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @param int $entity_id
     * @return AutoAuction
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getAuctionId()
    {
        return $this->getData(self::AUCTION_ID);
    }

    /**
     * @param $auction_id
     * @return AutoAuction
     */
    public function setAuctionId($auction_id)
    {
        return $this->setData(self::AUCTION_ID, $auction_id);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @param $status
     * @return AutoAuction
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @param $product_id
     * @return AutoAuction
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * @return mixed|array|string|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @param $customer_id
     * @return AutoAuction
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * @return array|float|mixed|null
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @param $amount
     * @return AutoAuction
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * @return array|float|mixed|null
     */
    public function getWinningPrice()
    {
        return $this->getData(self::WINNING_PRICE);
    }

    /**
     * @param $winning_price
     * @return AutoAuction
     */
    public function setWinningPrice($winning_price)
    {
        return $this->setData(self::WINNING_PRICE, $winning_price);
    }

    /**
     * @return mixed|array|null
     */
    public function getShop()
    {
        return $this->getData(self::SHOP);
    }

    /**
     * @param $shop
     * @return AutoAuction|mixed
     */
    public function setShop($shop)
    {
        return $this->setData(self::SHOP, $shop);
    }

    /**
     * @return mixed|array|string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param $created_at
     * @return AutoAuction
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getFlag()
    {
        return $this->getData(self::FLAG);
    }


    /**
     * @param $flag
     * @return AutoAuction
     */
    public function setFlag($flag)
    {
        return $this->setData(self::FLAG, $flag);
    }

    /**
     * @inheritdoc
     */
    public function getDataArray()
    {
        return $this->__toArray();
    }
}
