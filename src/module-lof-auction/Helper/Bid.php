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

namespace Lof\Auction\Helper;

use Exception;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuction;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory as AuctCollFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\WinnerData;
use Lof\Auction\Model\WinnerDataFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Bid
 *
 * @package Lof\Auction\Helper
 */
class Bid extends AbstractHelper
{
    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var AutoAuctionFactory
     */
    protected $_autoAuction;

    /**
     * @var AuctCollFactory
     */
    protected $_auctionProFactory;

    /**
     * @var AmountFactorye
     */
    protected $_aucAmountFactory;
    /**
     * @var customer
     */
    protected $customer;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var WinnerData
     */
    protected $_winnerData;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var Email
     */
    protected $_helperEmail;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var AmountFactory
     */
    private $_auctionAmount;

    protected $_helperData;

    /**
     * @param Context                               $context
     * @param DateTime                              $dateTime
     * @param TimezoneInterface                     $timezoneInterface
     * @param PriceHelper                           $priceHelper
     * @param AuctCollFactory                       $auctionProFactory
     * @param AmountFactory                         $aucAmountFactory
     * @param AutoAuction                           $autoAuction
     * @param AmountFactory                         $auctionAmount
     * @param StoreManagerInterface                 $storeManager
     * @param \Magento\Catalog\Model\ProductFactory $productCollectionFactory
     * @param CustomerFactory                       $customer
     * @param PriceCurrencyInterface                $priceFormatter
     * @param WinnerDataFactory                     $winnerData
     * @param ProductFactory                        $auctionProductFactory
     * @param Email                                 $helperEmail
     * @param CustomerSession                       $customerSession
     * @param Data $helperData
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        TimezoneInterface $timezoneInterface,
        PriceHelper $priceHelper,
        AuctCollFactory $auctionProFactory,
        AmountFactory $aucAmountFactory,
        AutoAuction $autoAuction,
        AmountFactory $auctionAmount,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productCollectionFactory,
        CustomerFactory $customer,
        PriceCurrencyInterface $priceFormatter,
        WinnerDataFactory $winnerData,
        ProductFactory $auctionProductFactory,
        Email $helperEmail,
        CustomerSession $customerSession,
        Data $helperData
    ) {
        $this->_storeManager            = $storeManager;
        $this->_dateTime                = $dateTime;
        $this->_auctionProFactory       = $auctionProFactory;
        $this->_timezoneInterface       = $timezoneInterface;
        $this->priceHelper              = $priceHelper;
        $this->_aucAmountFactory        = $aucAmountFactory;
        $this->_autoAuction             = $autoAuction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_customerSession         = $customerSession;
        $this->customer                 = $customer;
        $this->priceFormatter           = $priceFormatter;
        $this->_winnerData              = $winnerData;
        $this->_auctionProductFactory   = $auctionProductFactory;
        $this->_auctionAmount           = $auctionAmount;
        $this->_helperEmail             = $helperEmail;
        $this->_helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @param $product_id
     * @return Product|Collection|AbstractCollection
     */
    public function getProductById($product_id)
    {
        return $this->productCollectionFactory->create()->load($product_id);
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->priceFormatter->getCurrency()->getCurrencyCode();
    }

    /**
     * @param $price
     * @return string
     */
    public function getPriceFormat($price)
    {
        $currencyCode = $this->getCurrentCurrencyCode();

        return $this->priceFormatter->format(
            $price,
            false,
            null,
            null,
            $currencyCode
        );
    }

    /**
     * Get customer id
     *
     * @return customer id
     */
    public function getCustomerById($customer_id)
    {
        $collection = $this->customer->create()->load($customer_id);

        return $collection;
    }

    /**
     * Return brand config value by key and store
     *
     * @param string                                $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);

        return $this->scopeConfig->getValue(
            'lofauction/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get Configuration Detail of Auction
     *
     * @return array of Auction Configuration Detail
     */

    public function getAuctionConfiguration()
    {
        return [
            'enable' => $this->getConfig('general_settings/enable'),
            'auction_rule' => $this->getConfig('general_settings/auction_rule'),
            'show_bidder' => $this->getConfig('general_settings/show_bidder'),
            'show_price' => $this->getConfig('general_settings/show_price'),
            'reserve_price' => $this->getConfig('reserve_option/price'),
            'show_curt_auc_price' => $this->getConfig('general_settings/show_curt_auc_price'),
            'show_auc_detail' => $this->getConfig('general_settings/show_auc_detail'),
            'auto_enable' => $this->getConfig('auto/enable'),
            'auto_auc_limit' => $this->getConfig('auto/limit'),
            'show_auto_details' => $this->getConfig('auto/show_auto_details'),
            'auto_use_increment' => $this->getConfig('auto/use_increment'),
            'show_autobidder_name' => $this->getConfig('auto/show_autobidder_name'),
            'show_auto_bid_amount' => $this->getConfig('auto/show_bid_amount'),
            'show_auto_outbid_msg' => $this->getConfig('auto/show_auto_outbid_msg'),
            'enable_auto_outbid_msg' => $this->getConfig('auto/enable_auto_outbid_msg'),
            'disable_manual' => $this->getConfig('auto/disable_manual'),
            'show_winner_msg' => $this->getConfig('general_settings/show_winner_msg'),
            'increment_auc_enable' => $this->getConfig('increment_option/enable'),
            'enable_admin_email' => $this->getConfig('emails/enable_admin_email'),
            'admin_notify_email_template' => $this->getConfig('emails/admin_notify_email_template'),
            'enable_outbid_email' => $this->getConfig('emails/enable_outbid_email'),
            'outbid_notify_email_template' => $this->getConfig('emails/outbid_notify_email_template'),
            'enable_winner_notify_email' => $this->getConfig('emails/enable_winner_notify_email'),
            'winner_notify_email_template' => $this->getConfig('emails/winner_notify_email_template'),
            'admin_email_address' => $this->getConfig('emails/admin_email_address'),
        ];
    }

    /**
     * @param object $product
     * @return string string
     * @throws Exception
     */
    public function getProductAuctionDetail($product)
    {
        $modEnable = $this->getConfig('general_settings/enable');
        $content   = "";
        if ($modEnable) {
            $auctionOpt = $product->getAuctionType();
            $auctionOpt = explode(',', $auctionOpt);

            $auctionData  = $this->_auctionProFactory->create()
                                                     ->addFieldToFilter('product_id', $product->getEntityId())
                                                     ->addFieldToFilter('status', 0)
                                                     ->addFieldToFilter('auction_status', 1)
                                                     ->getFirstItem()->getData();
            $clock        = "";
            $htmlDataAttr = "";
            if ($auctionData) {
                $today            = $this->_timezoneInterface->date()->format('m/d/y H:i:s');
                $startAuctionTime = $this->_timezoneInterface->date(new \DateTime($auctionData['start_auction_time']))
                                                             ->format('m/d/y H:i:s');
                $stopAuctionTime  = $this->_timezoneInterface->date(new \DateTime($auctionData['stop_auction_time']))
                                                             ->format('m/d/y H:i:s');
                $difference       = strtotime($stopAuctionTime) - strtotime($today);
                if ($difference > 0 && $startAuctionTime < $today) {
                    $clock = '<p class="lofcat_count_clock" data-stoptime="' . $auctionData['stop_auction_time']
                             . '" data-diff_timestamp ="' . $difference . '"></p>';
                }
            }

            $auctionData = $this->_auctionProFactory->create()
                                                    ->addFieldToFilter('product_id', ['eq' => $product->getEntityId()])
                                                    ->addFieldToFilter('status', ['eq' => 0])->getFirstItem()->getData();

            if (isset($auctionData['entity_id'])) {
                $winnerBidDetail = $this->getWinnerBidDetail($auctionData['entity_id']);
                if ($winnerBidDetail && $this->_customerSession->isLoggedIn()) {
                    $winnerCustomerId  = $winnerBidDetail->getCustomerId();
                    $currentCustomerId = $this->_customerSession->getCustomerId();
                    if ($currentCustomerId == $winnerCustomerId) {
                        $price         = $winnerBidDetail->getBidType() == 'normal' ? $winnerBidDetail->getAuctionAmount() :
                            $winnerBidDetail->getWinningPrice();
                        $formatedPrice = $this->priceHelper->currency($price, true, false);
                        $htmlDataAttr  = 'data-winner="1" data-winning-amt="' . $formatedPrice . '"';
                    }
                }
            }

            /**
             * 2 : use for auction
             * 1 : use for Buy it now
             */
            if (in_array(2, $auctionOpt) && in_array(1, $auctionOpt)) {
                $content = '<div class="auction buy-it-now " ' . $htmlDataAttr . '>' . $clock . '</div>';
            } elseif (in_array(2, $auctionOpt)) {
                $content = '<div class="auction" ' . $htmlDataAttr . '>' . $clock . '</div>';
            } elseif (in_array(1, $auctionOpt)) {
                $content = '<div class="buy-it-now"></div>';
            }
        }

        return $content;
    }

    /**
     * $auctionId
     * @param int $auctionId auction product id
     * @return AmountFactory || AutoAuctionFactory
     */
    public function getWinnerBidDetail($auctionId)
    {
        $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
                                              ->addFieldToFilter('auction_id', ['eq' => $auctionId])
                                              ->addFieldToFilter('winning_status', ['eq' => 1])
                                              ->addFieldToFilter('status', ['eq' => 0])->getFirstItem();

        if ($aucAmtData->getEntityId()) {
            $aucAmtData->setBidType('normal');

            return $aucAmtData;
        } else {
            $aucAmtData = $this->_autoAuction->getCollection()
                                             ->addFieldToFilter('auction_id', ['eq' => $auctionId])
                                             ->addFieldToFilter('flag', ['eq' => 1])
                                             ->addFieldToFilter('status', ['eq' => 0])->getFirstItem();
            if ($aucAmtData->getEntityId()) {
                $aucAmtData->setBidType('auto');

                return $aucAmtData;
            }
        }

        return false;
    }

    /**
     * get incremental price value
     *
     * @return bool|float
     * @var float
     * @var float $minAmount
     */
    public function getIncrementPriceAsRange($minAmount)
    {
        $incPriceRang = $this->getConfig('increment_option/price_range');

        $incPriceRang = json_decode($incPriceRang, true);
        if ($incPriceRang) {
            foreach ($incPriceRang as $range => $value) {
                $range = explode('-', $range);
                if ($minAmount >= $range[0] && $minAmount <= $range[1]) {
                    return floatval($value);
                }
            }
        }

        return false;
    }

    /**
     * get Active Auction Id
     *
     * @param $productId int
     * @return int|false
     */
    public function getActiveAuctionId($productId)
    {
        $auctionData = $this->_auctionProFactory->create()
                                                ->addFieldToFilter('product_id', ['eq' => $productId])
                                                ->addFieldToFilter('status', ['eq' => 0])
                                                ->setOrder('entity_id')->getFirstItem();

        return $auctionData->getEntityId() ? $auctionData->getEntityId() : false;
    }

    /**
     * biddingOperation
     *
     * @param int $productId on which auction apply
     * @return void
     */
    public function biddingOperation($productId)
    {
        $winnerCustomerId = '';
        $bidDay           = '';
        $stop             = '';
        $addToWin         = 0;
        $auctionConfig    = $this->getAuctionConfiguration();
        $auctionActPro    = $this->_auctionProductFactory->create()->getCollection()
                                                         ->addFieldToFilter('product_id', ['eq' => $productId])
                                                         ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS)//STATUS_PROCESS
                                                         ->getFirstItem();
        if ($auctionActPro->getEntityId()) {
            $bidDay = $auctionActPro->getDays();
            $bidId  = $auctionActPro->getEntityId();

            $incPrice = $auctionActPro->getIncrementPrice();
            $resPrice = $auctionActPro->getReservePrice();

            $datestop  = strtotime($auctionActPro->getStopAuctionTime());
            $datestart = strtotime($auctionActPro->getStartAuctionTime());

            if ($datestop >= $datestart) {
                $winDataTemp['auction_id'] = $auctionActPro->getEntityId();
                $today                     = $this->_dateTime->date('Y-m-d H:i:s');
                $current                   = strtotime($today);
                $difference                = $datestop - $current;

                if ($difference <= 0) {
                    $bidArray = [];

                    $bidAmountList = $this->_auctionAmount->create()->getCollection()
                                                          ->addFieldToFilter('product_id', ['eq' => $productId])
                                                          ->addFieldToFilter('auction_id', ['eq' => $bidId])
                                                          ->addFieldToFilter('status', ['eq' => 1])
                                                          ->setOrder('auction_amount', 'ASC');
                    foreach ($bidAmountList as $bidAmt) {
                        $bidArray[$bidAmt->getCustomerId()] = $bidAmt->getAuctionAmount();
                    }

                    //arsort($bidArray);
                    $autoBidArray = [];
                    /***/
                    if (count($bidArray)) {
                        $customerIds                = array_keys($bidArray, max($bidArray));
                        $winDataTemp['customer_id'] = $customerIds[0];
                        $winDataTemp['amount']      = max($bidArray);
                        $winDataTemp['type']        = 'normal';
                    }
                    $autoBidFlag = false;
                    if ($auctionConfig['auto_enable'] && $auctionActPro->getAutoAuctionOpt()) {
                        $autoBidList = $this->_autoAuction->getCollection()
                                                          ->addFieldToFilter('auction_id', ['eq' => $bidId])
                                                          ->addFieldToFilter('status', ['eq' => 1]);
                        if (count($bidArray)) {
                            $autoBidList->addFieldToFilter('amount', ['gteq' => max($bidArray)]);
                            $winDataTemp['amount'] = max($bidArray);
                        } else {
                            $starPrice             = $auctionActPro->getStartingPrice();
                            $winDataTemp['amount'] = $resPrice ? $resPrice : $starPrice;
                        }

                        if (count($autoBidList)) {
                            foreach ($autoBidList as $autoBid) {
                                $custId                = $autoBid->getCustomerId();
                                $autoBidArray[$custId] = $autoBid->getAmount();
                            }
                            $customerIds                = array_keys($autoBidArray, max($autoBidArray));
                            $winDataTemp['customer_id'] = $customerIds[0];
                            $winDataTemp['type']        = 'auto';
                        }

                        if ($auctionConfig['increment_auc_enable'] && $auctionActPro->getIncrementOpt()
                            && isset($winDataTemp['customer_id']) && $winDataTemp['type'] = 'auto') {
                            $incVal                = $this->getIncrementPriceAsRange($winDataTemp['amount']);
                            $incMinPrice           = $incVal ? $incVal : 0;
                            $winDataTemp['amount'] = $winDataTemp['amount'] + $incMinPrice;
                        }
                    }

                    if (isset($winDataTemp['customer_id']) && $resPrice <= $winDataTemp['amount']) {
                        if ($winDataTemp['type'] == 'auto') {
                            $autoBiddList = $this->_autoAuction->getCollection()
                                                               ->addFieldToFilter('auction_id', ['eq' => $bidId])
                                                               ->addFieldToFilter('status', 1);

                            foreach ($autoBiddList as $autoBid) {
                                if ($autoBid->getCustomerId() == $winDataTemp['customer_id']) {
                                    $autoBid->setFlag(1);
                                    $autoBid->setWinningPrice($winDataTemp['amount']);
                                    /** send notification mail to winner */
                                    if ($auctionConfig['enable_winner_notify_email']) {
                                        $this->_helperEmail->sendWinnerMail(
                                            $winDataTemp['customer_id'],
                                            $productId,
                                            $winDataTemp['amount']
                                        );
                                    }
                                }
                                $autoBid->setStatus(0);
                                $autoBid->setId($autoBid->getEntityId());
                                $this->saveObj($autoBid);
                            }
                        } elseif ($winDataTemp['type'] == 'normal') {
                            $normalBidList = $this->_auctionAmount->create()->getCollection()
                                                                  ->addFieldToFilter('product_id', ['eq' => $productId])
                                                                  ->addFieldToFilter('auction_id', ['eq' => $bidId])
                                                                  ->addFieldToFilter('status', ['eq' => 1]);

                            foreach ($normalBidList as $normalBid) {
                                if ($normalBid->getCustomerId() == $winDataTemp['customer_id']) {
                                    $normalBid->setWinningStatus(1);
                                    /** send notification mail to winner */
                                    if ($auctionConfig['enable_winner_notify_email']) {
                                        $this->_helperEmail->sendWinnerMail(
                                            $winDataTemp['customer_id'],
                                            $productId,
                                            $winDataTemp['amount']
                                        );
                                    }
                                }
                                $normalBid->setStatus(0);
                                $normalBid->setId($normalBid->getEntityId());
                                $this->saveObj($normalBid);
                            }
                        }

                        //save winner record in winner data table
                        $winnerDataModel             = $this->_winnerData->create();
                        $auctionModel                = $this->_auctionProductFactory->create()->load($bidId)->getData();
                        $auctionModel['customer_id'] = $winDataTemp['customer_id'];
                        $auctionModel['status']      = 1;
                        $auctionModel['auction_id']  = $auctionModel['entity_id'];
                        $auctionModel['win_amount']  = $winDataTemp['amount'];//$auctionModel['min_amount'];
                        unset($auctionModel['entity_id']);
                        $winnerDataModel->setData($auctionModel);

                        $this->saveObj($winnerDataModel);
                        $auctionActPro->setAuctionStatus(AuctionStatus::STATUS_TIME_END);
                    } else {
                        $auctionActPro->setAuctionStatus(AuctionStatus::STATUS_WINNER_NOT_DEFINED);
                    }

                    $auctionActPro->setId($auctionActPro->getEntityId());

                    $this->saveObj($auctionActPro);
                    /***/
                } else {
                    $winnerDataModel = $this->_winnerData->create()->getCollection()
                                                         ->addFieldToFilter('product_id', ['eq' => $productId])
                                                         ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS])
                                                         ->getFirstItem();

                    if ($winnerDataModel->getEntityId()) {
                        $bidDay     = !$winnerDataModel->getDays() ? 1 : (int)$winnerDataModel->getDays();
                        $current = strtotime($this->_helperData->getTimezoneDateTime());
                        $stopAuctionTime = $this->_helperData->getTimezoneDateTime($winnerDataModel->getStopAuctionTime());
                        $day        = strtotime($stopAuctionTime . ' + ' . $bidDay . ' days');
                        $difference = $day - $current;
                        if ($difference < 0) {
                            $winnerDataModel->setStatus((AuctionStatus::STATUS_TIME_END));
                            $winnerDataModel->setId($winnerDataModel->getEntityId());
                            $this->saveObj($winnerDataModel);
                        }
                    }
                }
            }
        }
    }

    /**
     * saveObj
     *
     * @param Object
     * @return void
     */
    private function saveObj($object)
    {
        $object->save();
    }
}
