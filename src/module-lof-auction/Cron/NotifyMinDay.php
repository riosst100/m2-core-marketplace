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
namespace Lof\Auction\Cron;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\Email;
use Lof\Auction\Model\Amount;
use Lof\Auction\Model\AutoAuction;
use Lof\Auction\Model\ResourceModel\SubscriberAddress\CollectionFactory as SubscriberCollectionFactory;
use Lof\Auction\Model\Product;
use Lof\Auction\Model\ProductFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class NotifyMinDay
 * @package Lof\Auction\Cron
 */
class NotifyMinDay
{
    /**
     * @var Data
     */
    protected $_dataHelper;
    /**
     * @var Amount
     */
    protected $_auctionAmount;
    /**
     * @var AutoAuction
     */
    protected $_auctionAutoAmount;
    /**
     * @var Product
     */
    private $_auction;
    /**
     * @var SubscriberCollectionFactory
     */
    private $_subscriberCollectionFactory;
    /**
     * @var Email
     */
    private $_helperEmail;
    /**
     * @var ProductFactory
     */
    private $_auctionFactory;
    /**
     * @var TimezoneInterface
     */
    private $_localeDate;

    /**
     * NotifyMinDay constructor.
     * @param Data $_dataHelper
     * @param Email $helperEmail
     * @param Amount $amount
     * @param AutoAuction $autoAuction
     * @param Product $auction
     * @param SubscriberCollectionFactory $subscriberCollectionFactory
     * @param ProductFactory $_auctionFactory
     * @param TimezoneInterface $localeDate
     */
    public function __construct(
        Data $_dataHelper,
        Email $helperEmail,
        Amount $amount,
        AutoAuction $autoAuction,
        Product $auction,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        ProductFactory $_auctionFactory,
        TimezoneInterface $localeDate
    ) {
        $this->_dataHelper= $_dataHelper;
        $this->_helperEmail = $helperEmail;
        $this->_auctionAmount = $amount;
        $this->_auctionAutoAmount = $autoAuction;
        $this->_auction = $auction;
        $this->_subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->_auctionFactory = $_auctionFactory;
        $this->_localeDate = $localeDate;
    }


    /**
     * Excecure cron job
     * @return void
     */
    public function execute()
    {
        $auctionConfig = $this->_dataHelper->getAuctionConfiguration();
        if (!$auctionConfig['enable_min_day_notify']) {
            return;
        }
        $notify_day = $auctionConfig['min_day_notify'];
        $notify_hour = $auctionConfig['min_hours_notify'];
        if ($notify_day || $notify_hour) {
            $listAuction = $this->_auction->getListSooningAuction($notify_day, $notify_hour);
            if($listAuction->count()){
                $listAuctionIds = [];
                foreach ($listAuction as $auction) {
                    $listAuctionIds[] = $auction->getEntityId();
                }
                $subscriberCollection = $this->_subscriberCollectionFactory->create();
                $subscriberCollection->addFieldToFilter('auction_id', ['in' => $listAuctionIds])
                    ->addFieldToFilter('is_sent', 0);

                foreach ($subscriberCollection as $subscriber) {
                    $auction = $this->_auctionFactory->create()->load($subscriber->getAuctionId());
                    $stopTimeUtc = $auction->getData('stop_auction_time');
                    $stopTime = $this->_dataHelper->getTimezoneDateTime($stopTimeUtc);
                    $this->_helperEmail->sendMailNotifyMinDay($subscriber->getCustomerId(), $auction->getProductId(), $stopTime);
                    $subscriber->setData('is_sent', 1);
                    $subscriber->save();
                }
            }
        }
    }
}
