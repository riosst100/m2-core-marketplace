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

use Lof\Auction\Api\Data\WinnerDataInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;

/**
 * Class WinnerData
 * @package Lof\Auction\Model
 */
class WinnerData extends AbstractAuctionModel implements WinnerDataInterface
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
        $this->_init('Lof\Auction\Model\ResourceModel\WinnerData');
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
    public function getWinAmount()
    {
        return $this->getData(self::WIN_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setWinAmount($win_amount)
    {
        return $this->setData(self::WIN_AMOUNT, $win_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getDays()
    {
        return $this->getData(self::DAYS);
    }

    /**
     * {@inheritdoc}
     */
    public function setDays($days)
    {
        return $this->setData(self::DAYS, $days);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinQty()
    {
        return $this->getData(self::MIN_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinQty($min_qty)
    {
        return $this->setData(self::MIN_QTY, $min_qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxQty()
    {
        return $this->getData(self::MAX_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxQty($max_qty)
    {
        return $this->setData(self::MAX_QTY, $max_qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartAuctionTime()
    {
        return $this->getData(self::START_AUCTION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartAuctionTime($start_auction_time)
    {
        return $this->setData(self::START_AUCTION_TIME, $start_auction_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getStopAuctionTime()
    {
        return $this->getData(self::STOP_AUCTION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setStopAuctionTime($stop_auction_time)
    {
        return $this->setData(self::STOP_AUCTION_TIME, $stop_auction_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getComplete()
    {
        return $this->getData(self::COMPLETE);
    }

    /**
     * {@inheritdoc}
     */
    public function setComplete($complete)
    {
        return $this->setData(self::COMPLETE, $complete);
    }

    /**
     * @inheritdoc
     */
    public function getDataArray()
    {
        return $this->__toArray();
    }
}
