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

use Lof\Auction\Api\Data\ProductInterface;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Product
 * @package Lof\Auction\Model
 */
class Product extends AbstractAuctionModel implements ProductInterface
{
    /**
     * @var DateTime
     */
    private $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Menu cache tag
     * @var string
     */
    const CACHE_TAG = 'auction_product';

    /**
     * Initialize resource model
     *
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezoneInterface
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        TimezoneInterface $timezoneInterface,
        Data $helperData
    ) {
        $this->_dateTime = $dateTime;
        $this->_timezoneInterface = $timezoneInterface;
        $this->helperData = $helperData;

        parent::__construct($context, $registry);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Lof\Auction\Model\ResourceModel\Product');
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
    public function getStartingPrice()
    {
        return $this->getData(self::STARTING_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartingPrice($starting_price)
    {
        return $this->setData(self::STARTING_PRICE, $starting_price);
    }

    /**
     * {@inheritdoc}
     */
    public function getReservePrice()
    {
        return $this->getData(self::RESERVE_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservePrice($reserve_price)
    {
        return $this->setData(self::RESERVE_PRICE, $reserve_price);
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
    public function getMaxAmount()
    {
        return $this->getData(self::MAX_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxAmount($max_amount)
    {
        return $this->setData(self::MAX_AMOUNT, $max_amount);
    }

    /**
     * {@inheritdoc}
     */
    public function getMinAmount()
    {
        return $this->getData(self::MIN_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setMinAmount($min_amount)
    {
        return $this->setData(self::MIN_AMOUNT, $min_amount);
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
    public function getAuctionStatus()
    {
        return $this->getData(self::AUCTION_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuctionStatus($auction_status)
    {
        return $this->setData(self::AUCTION_STATUS, $auction_status);
    }

    /**
     * {@inheritdoc}
     */
    public function getIncrementOpt()
    {
        return $this->getData(self::INCREMENT_OPT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIncrementOpt($increment_opt)
    {
        return $this->setData(self::INCREMENT_OPT, $increment_opt);
    }

    /**
     * {@inheritdoc}
     */
    public function getIncrementPrice()
    {
        return $this->getData(self::INCREMENT_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIncrementPrice($increment_price)
    {
        return $this->setData(self::INCREMENT_PRICE, $increment_price);
    }

    /**
     * {@inheritdoc}
     */
    public function getAutoAuctionOpt()
    {
        return $this->getData(self::AUTO_AUCTION_OPT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAutoAuctionOpt($auto_auction_opt)
    {
        return $this->setData(self::AUTO_AUCTION_OPT, $auto_auction_opt);
    }

    /**
     * {@inheritdoc}
     */
    public function getFeaturedAuction()
    {
        return $this->getData(self::FEATURED_AUCTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setFeaturedAuction($featured_auction)
    {
        return $this->setData(self::FEATURED_AUCTION, $featured_auction);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuyItNow()
    {
        return $this->getData(self::BUY_IT_NOW);
    }

    /**
     * {@inheritdoc}
     */
    public function setBuyItNow($buy_it_now)
    {
        return $this->setData(self::BUY_IT_NOW, $buy_it_now);
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
     * {@inheritdoc}
     */
    public function getCurrentTimeStamp()
    {
        return $this->getData(self::CURRENT_TIME_STAMP);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentTimeStamp($current_time_stamp)
    {
        return $this->setData(self::CURRENT_TIME_STAMP, $current_time_stamp);
    }

    /**
     * {@inheritdoc}
     */
    public function getContinueTime()
    {
        return $this->getData(self::CONTINUE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setContinueTime($continue_time)
    {
        return $this->setData(self::CONTINUE_TIME, $continue_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getLimitBids()
    {
        return $this->getData(self::LIMIT_BIDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimitBids($limit_bids)
    {
        return $this->setData(self::LIMIT_BIDS, $limit_bids);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeZone()
    {
        return $this->getData(self::TIME_ZONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimeZone($time_zone)
    {
        return $this->setData(self::TIME_ZONE, $time_zone);
    }

    /**
     * {@inheritdoc}
     */
    public function getStopAuctionUtcTime()
    {
        return $this->getData(self::STOP_AUCTION_UTC_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setStopAuctionUtcTime($stop_auction_utc_time)
    {
        return $this->setData(self::STOP_AUCTION_UTC_TIME, $stop_auction_utc_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartAuctionUtcTime()
    {
        return $this->getData(self::START_AUCTION_UTC_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartAuctionUtcTime($start_auction_utc_time)
    {
        return $this->setData(self::START_AUCTION_UTC_TIME, $start_auction_utc_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getTodayTime()
    {
        return $this->getData(self::TODAY_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setTodayTime($today_time)
    {
        return $this->setData(self::TODAY_TIME, $today_time);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartAuctionTimeStamp()
    {
        return $this->getData(self::START_AUCTION_TIME_STAMP);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartAuctionTimeStamp($start_auction_time_stamp)
    {
        return $this->setData(self::START_AUCTION_TIME_STAMP, $start_auction_time_stamp);
    }

    /**
     * {@inheritdoc}
     */
    public function getStopAuctionTimeStamp()
    {
        return $this->getData(self::STOP_AUCTION_TIME_STAMP);
    }

    /**
     * {@inheritdoc}
     */
    public function setStopAuctionTimeStamp($stop_auction_time_stamp)
    {
        return $this->setData(self::STOP_AUCTION_TIME_STAMP, $stop_auction_time_stamp);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuctionStart()
    {
        return $this->getData(self::NEW_AUCTION_START);
    }

    /**
     * {@inheritdoc}
     */
    public function setNewAuctionStart($new_auction_start)
    {
        return $this->setData(self::NEW_AUCTION_START, $new_auction_start);
    }

    /**
     * {@inheritdoc}
     */
    public function getProUrl()
    {
        return $this->getData(self::PRO_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setProUrl($pro_url)
    {
        return $this->setData(self::PRO_URL, $pro_url);
    }

    /**
     * {@inheritdoc}
     */
    public function getProName()
    {
        return $this->getData(self::PRO_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setProName($pro_name)
    {
        return $this->setData(self::PRO_NAME, $pro_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerName($customer_name)
    {
        return $this->setData(self::CUSTOMER_NAME, $customer_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxBids()
    {
        return $this->getData(self::MAX_BIDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxBids($max_bids)
    {
        return $this->setData(self::MAX_BIDS, $max_bids);
    }

    /**
     * @param int $min_day
     * @return mixed
     * @throws LocalizedException
     */
    public function getListSooningAuction($min_day = 0, $min_hour = 0)
    {
        $currentTime = $this->helperData->getTimezoneDateTime();
        $endTime = strtotime($currentTime."+".$min_day." days + ".$min_hour." hours");
        $end = date("Y-m-d H:i:s", $endTime);
        $collection = $this->getCollection()
            ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS)
            //->addFieldToFilter('status', 0)
            ->addFieldToFilter('stop_auction_time', ['from' => $currentTime, 'to' => $end]);
        $collection->addFieldToSelect('entity_id');
        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function getDataArray()
    {
        return $this->__toArray();
    }
}
