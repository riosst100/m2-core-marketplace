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
use Lof\Auction\Model\ResourceModel\History\CollectionFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory as AuctCollFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\WinnerDataFactory;
use Lof\Auction\Model\ProductFactory as AuctionProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\FormatInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Data extends AbstractHelper
{
    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
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
     * @var CustomerFactory
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
     * @var \Lof\Auction\Model\ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var AmountFactory
     */
    private $_auctionAmount;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var WinnerDataFactory
     */
    private $_winnerData;

    /**
     * @var CurrencyFactory
     */
    private $_dirCurrencyFactory;

    /**
     * @var MixAmountCollectionFactory
     */
    private $_mixAmountCollectionFactory;

    /**
     * @var DateTime
     */
    private $date;
    /**
     * @var CollectionFactory
     */
    private $historyCollection;

    /**
     * @var FormatInterface
     */
    protected $localeFormat;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param   DateTime $dateTime
     * @param   TimezoneInterface $timezoneInterface
     * @param   PriceHelper $priceHelper
     * @param   AuctCollFactory $auctionProFactory
     * @param   AmountFactory $aucAmountFactory
     * @param   AutoAuctionFactory $autoAuction
     * @param   FilterProvider $filterProvider
     * @param   AmountFactory $auctionAmount
     * @param   StoreManagerInterface $storeManager
     * @param   ProductFactory $productFactory
     * @param   CustomerFactory $customer
     * @param   PriceCurrencyInterface $priceFormatter
     * @param   WinnerDataFactory $winnerData
     * @param   AuctionProductFactory $auctionProductFactory
     * @param   CustomerSession $customerSession
     * @param   CurrencyFactory $dirCurrencyFactory
     * @param   MixAmountCollectionFactory $mixAmountCollectionFactory
     * @param   CollectionFactory $historyCollection
     * @param   FormatInterface $localeFormat
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        TimezoneInterface $timezoneInterface,
        PriceHelper $priceHelper,
        AuctCollFactory $auctionProFactory,
        AmountFactory $aucAmountFactory,
        AutoAuctionFactory $autoAuction,
        FilterProvider $filterProvider,
        AmountFactory $auctionAmount,
        StoreManagerInterface $storeManager,
        ProductFactory $productFactory,
        CustomerFactory $customer,
        PriceCurrencyInterface $priceFormatter,
        WinnerDataFactory $winnerData,
        AuctionProductFactory $auctionProductFactory,
        CustomerSession $customerSession,
        CurrencyFactory $dirCurrencyFactory,
        MixAmountCollectionFactory $mixAmountCollectionFactory,
        CollectionFactory $historyCollection,
        FormatInterface $localeFormat
    ) {
        $this->_storeManager = $storeManager;
        $this->_dateTime = $dateTime;
        $this->_auctionProFactory = $auctionProFactory;
        $this->_timezoneInterface = $timezoneInterface;
        $this->priceHelper = $priceHelper;
        $this->_aucAmountFactory = $aucAmountFactory;
        $this->_autoAuction = $autoAuction;
        $this->productFactory = $productFactory;
        $this->_customerSession = $customerSession;
        $this->customer = $customer;
        $this->priceFormatter = $priceFormatter;
        $this->_winnerData = $winnerData;
        $this->_auctionProductFactory = $auctionProductFactory;
        $this->_auctionAmount = $auctionAmount;
        $this->_filterProvider = $filterProvider;
        $this->_dirCurrencyFactory = $dirCurrencyFactory;
        $this->_mixAmountCollectionFactory = $mixAmountCollectionFactory;
        $this->historyCollection = $historyCollection;
        $this->date = $dateTime;
        $this->localeFormat = $localeFormat;
        parent::__construct($context);
    }

    /**
     * get date time
     * @return getAuctionConfiguration
     */
    public function getDateTime()
    {
        return $this->_dateTime;
    }

    /**
     * get timezone date time
     *
     * @param string $dateTime
     * @return string
     */
    public function getTimezoneDateTime($dateTime = "today")
    {
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->_dateTime->gmtDate();
        }

        $today = $this->_timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * get timezone name
     * @return string
     */
    public function getTimezoneName()
    {
        return $this->_timezoneInterface->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }

    /**
     * Get price format
     * @return mixed|string
     */
    public function getPriceFormatType()
	{
		return $this->localeFormat->getPriceFormat();
	}

    /**
     * @param $str
     * @return string
     * @throws Exception
     */
    public function filter($str)
    {
        return $this->_filterProvider->getPageFilter()->filter($str);
    }

    /**
     * @return TimezoneInterface
     */
    public function getTimezone()
    {
        return $this->_timezoneInterface;
    }

    /**
     * Get getGmtOffset
     * @return time zone
     */
    public function getGmtOffset()
    {
        return $this->_dateTime->getGmtOffset();
    }

    /**
     * Get product id
     * @param $product_id
     * @return Product
     */
    public function getProductById($product_id)
    {
        return $this->productFactory->create()->load($product_id);
    }

    /**
     * @param $statusNumber
     * @return mixed
     */
    public function getAuctionStatus($statusNumber)
    {
        $statuses = [
            '0' => __('TIME END'),
            '1' => __('PROCESSING'),
            '2' => __('WINNER NOT DEFINED'),
            '3' => __('CANCELED'),
            '4' => __('COMPLETED'),
        ];
        return isset($statuses[$statusNumber])?$statuses[$statusNumber]:"";
    }

    /**
     * @param $statusNumber
     * @return mixed
     */
    public function getAuctionClass($statusNumber)
    {
        $statuses = [
            '0' => __('time-end'),
            '1' => __('processing'),
            '2' => __('winner-not-defined'),
            '3' => __('canceled'),
            '4' => __('completed'),
        ];
        return isset($statuses[$statusNumber])?$statuses[$statusNumber]:"";
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
        $currencyCode = $currencyCode ? $currencyCode : null;
        return $this->priceFormatter->format(
            $price,
            false,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            null,
            $currencyCode
        );
    }

    /**
     * Get customer id
     * @return customer id
     */
    public function getCustomerById($customer_id)
    {
        return $this->customer->create()->load($customer_id);
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param Store|int|string $store
     * @return string|null
     * @throws NoSuchEntityException
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
     * Return brand config value by key and store
     *
     * @param string $key
     * @param Store|int|string $store
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getAdvancedDisplayConfig($key, $store = null)
    {
        return $this->getConfig('advanced_display/'.$key, $store);
    }

    /**
     * Check module is enabled
     *
     * @return bool|int
     */
    public function isEnabled()
    {
        return (int)$this->getConfig("general_settings/enable");
    }

    /**
     * Is require place a bid before
     *
     * @return bool|int
     */
    public function isRequirePlaceBid()
    {
        return (int)$this->getConfig("auto/require_place_bid");
    }

    /**
     * Is allow set winner for absentee bidder if auction end of time but not have any bid
     *
     * @return bool|int
     */
    public function allowSetWinnerForAbsenteeBid()
    {
        return (int)$this->getConfig("auto/allow_set_winner_absentee");
    }

    /**
     * Get bidder name
     *
     * @param Object|mixed
     * @return string
     */
    public function getBidderName($bidder)
    {
        $lastName = $bidder ? $bidder->getLastname() : "";
        $lastName = !empty($lastName) ? substr_replace($lastName, "****", 1) : "";
        if ($this->getConfig('general_settings/use_customer_id')) {
            return $bidder->getId() . ' ' . $lastName;
        } else {
            return $bidder->getFirstname() . ' ' . $lastName;
        }
    }

    /**
     * Get Configuration Detail of Auction
     * @return mixed|array of Auction Configuration Detail
     */
    public function getAuctionConfiguration()
    {
        $auctionConfig = [
            'enable' => $this->getConfig('general_settings/enable'),
            'auction_rule' => $this->getConfig('general_settings/auction_rule'),
            'show_bidder' => $this->getConfig('general_settings/show_bidder'),
            'show_bidder_name' => (int)$this->getConfig('general_settings/show_bidder_name'),
            'use_customer_id' => $this->getConfig('general_settings/use_customer_id'),
            'show_price' => $this->getConfig('general_settings/show_price'),
            'show_confirm_bid' => $this->getConfig('general_settings/show_confirm_bid'),
            'confirm_message' => $this->getConfig('general_settings/confirm_message'),
            'starting_title' => $this->getConfig('auction_page/starting_title'),
            'ending_title' => $this->getConfig('auction_page/ending_title'),
            'show_curt_auc_price' => (int)$this->getConfig('general_settings/show_curt_auc_price'),
            'show_auc_detail' => (int)$this->getConfig('general_settings/show_auc_detail'),
            'auto_enable' => $this->getConfig('auto/enable'),
            'auto_auc_limit' => $this->getConfig('auto/limit'),
            'show_auto_details' => (int)$this->getConfig('auto/show_auto_details'),
            'step' => $this->getConfig('increment_option/default_step'),
            'auto_use_increment' => $this->getConfig('auto/use_increment'),
            'show_autobidder_name' => $this->getConfig('auto/show_autobidder_name'),
            'disable_manual' => $this->getConfig('auto/disable_manual'),
            'show_auto_bid_amount' => $this->getConfig('auto/show_bid_amount'),
            'show_auto_outbid_msg' => $this->getConfig('auto/show_auto_outbid_msg'),
            'enable_auto_outbid_msg' => $this->getConfig('auto/enable_auto_outbid_msg'),
            'enable_max_absentee_bid' => $this->getConfig('auto/enable_max_absentee_bid'),
            'show_winner_msg' => $this->getConfig('general_settings/show_winner_msg'),
            'increment_auc_enable' => $this->getConfig('increment_option/enable'),
            'price_range' => $this->getConfig('increment_option/price_range'),
            'enable_admin_email' => $this->getConfig('emails/enable_admin_email'),
            'admin_notify_email_template' => $this->getConfig('emails/admin_notify_email_template'),
            'enable_outbid_email' => $this->getConfig('emails/enable_outbid_email'),
            'enable_confirm_email' => $this->getConfig('emails/enable_confirm_email'),
            'confirm_email_template' => $this->getConfig('emails/confirm_email_template'),
            'outbid_notify_email_template' => $this->getConfig('emails/outbid_notify_email_template'),
            'enable_winner_notify_email' => $this->getConfig('emails/enable_winner_notify_email'),
            'winner_notify_email_template' => $this->getConfig('emails/winner_notify_email_template'),
            'admin_email_address' => $this->getConfig('emails/admin_email_address'),
            'restriction_enable' => $this->getConfig('restriction/enable'),
            'restriction_period' => $this->getConfig('restriction/period'),
            'restriction_times' => $this->getConfig('restriction/times'),
            'enable_min_day_notify' => $this->getConfig('emails/enable_min_day_notify'),
            'min_day_notify' => $this->getConfig('emails/min_day_notify'),
            'min_hours_notify' => $this->getConfig('emails/min_hours_notify'),
            'min_day_notify_email_template' => $this->getConfig('emails/min_day_notify_email_template'),
            'current_url' => $this->getCurrentUrl(),
            'require_place_bid' => (int)$this->isRequirePlaceBid()
        ];
        if (!$this->_customerSession->isLoggedIn()) {
            $auctionConfig["enable_max_absentee_bid"] = false;
        }
        return $auctionConfig;
    }

    /**
     * Get Configuration Detail of Auction
     * @return mixed|array of Auction Configuration Detail
     */
    public function getPublicAuctionConfigData()
    {
        $auctionConfig = $this->getAuctionConfiguration();

        unset($auctionConfig['enable_admin_email']);
        unset($auctionConfig['admin_notify_email_template']);
        unset($auctionConfig['enable_outbid_email']);
        unset($auctionConfig['enable_confirm_email']);
        unset($auctionConfig['confirm_email_template']);
        unset($auctionConfig['outbid_notify_email_template']);
        unset($auctionConfig['enable_winner_notify_email']);
        unset($auctionConfig['winner_notify_email_template']);
        unset($auctionConfig['admin_email_address']);
        unset($auctionConfig['min_day_notify_email_template']);
        unset($auctionConfig['admin_email_address']);
        unset($auctionConfig['admin_email_address']);
        unset($auctionConfig['admin_email_address']);

        $todayUtc = $this->getTimezoneDateTime();
        $show_max_price = (int)$this->getAdvancedDisplayConfig("show_max_price");
        $show_max_qty = (int)$this->getAdvancedDisplayConfig("show_max_qty");
        $show_min_price = (int)$this->getAdvancedDisplayConfig("show_min_price");
        $show_min_qty = (int)$this->getAdvancedDisplayConfig("show_min_qty");
        $show_start_price = (int)$this->getAdvancedDisplayConfig("show_start_price");
        $show_reserve_price = (int)$this->getAdvancedDisplayConfig("show_reserve_price");
        $show_timezone = (int)$this->getAdvancedDisplayConfig("show_timezone");
        $show_current_time = (int)$this->getAdvancedDisplayConfig("show_current_time");
        $show_bid_start = (int)$this->getAdvancedDisplayConfig("show_bid_start");
        $show_bid_end = (int)$this->getAdvancedDisplayConfig("show_bid_end");
        $expire_full_info = (int)$this->getAdvancedDisplayConfig("expire_full_info");

        $auctionConfig["todayUtc"] = $todayUtc;
        $auctionConfig["expire_full_info"] = $expire_full_info;
        $auctionConfig["show_max_price"] = $show_max_price;
        $auctionConfig["show_max_qty"] = $show_max_qty;
        $auctionConfig["show_min_price"] = $show_min_price;
        $auctionConfig["show_min_qty"] = $show_min_qty;
        $auctionConfig["show_start_price"] = $show_start_price;
        $auctionConfig["show_reserve_price"] = $show_reserve_price;
        $auctionConfig["show_timezone"] = $show_timezone;
        $auctionConfig["show_current_time"] = $show_current_time;
        $auctionConfig["show_bid_start"] = $show_bid_start;
        $auctionConfig["show_bid_end"] = $show_bid_end;
        $auctionConfig["storeCode"] = $this->_storeManager->getStore()->getCode();

        return $auctionConfig;
    }

    /**
     * @param object $product
     * @return string string
     * @throws NoSuchEntityException
     */
    public function getProductAuctionDetail($product)
    {
        $modEnable = $this->getConfig('general_settings/enable');
        $content = "";
        if ($modEnable) {
            $auctionOpt = $product->getAuctionType();
            $auctionOpt = explode(',', $auctionOpt);

            $auctionData = $this->_auctionProFactory->create()
                ->addFieldToFilter('product_id', $product->getEntityId())
                ->addFieldToFilter('status', 0)
                ->addFieldToFilter('auction_status', 1)
                ->getFirstItem()->getData();
            $clock = "";
            $htmlDataAttr = "";
            if ($auctionData) {
                $today = $this->_timezoneInterface->date()->format('m/d/y H:i:s');
                $startAuctionTime = $this->_timezoneInterface->date(new \DateTime($auctionData['start_auction_time']))
                    ->format('m/d/y H:i:s');
                $stopAuctionTime = $this->_timezoneInterface->date(new \DateTime($auctionData['stop_auction_time']))
                    ->format('m/d/y H:i:s');
                $difference = strtotime($stopAuctionTime) - strtotime($today);
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
                    $winnerCustomerId = $winnerBidDetail->getCustomerId();
                    $currentCustomerId = $this->_customerSession->getCustomerId();
                    if ($currentCustomerId == $winnerCustomerId) {
                        $price = $winnerBidDetail->getBidType() == 'normal' ? $winnerBidDetail->getAuctionAmount() :
                            $winnerBidDetail->getWinningPrice();
                        $formatedPrice = $this->priceHelper->currency($price, true, false);
                        $htmlDataAttr = 'data-winner="1" data-winning-amt="' . $formatedPrice . '"';
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
        $aucAmtData = $this->_aucAmountFactory->create()
            ->getCollection()
            ->addFieldToFilter('auction_id', ['eq' => $auctionId])
            ->addFieldToFilter('winning_status', ['eq' => 1])
            ->addFieldToFilter('status', ['eq' => 0])->getFirstItem();

        if ($aucAmtData->getEntityId()) {
            $aucAmtData->setBidType('normal');
            return $aucAmtData;
        } else {
            $aucAmtData = $this->_autoAuction->create()->getCollection()
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
     * @var float $minAmount
     * @var float
     */
    public function getIncrementPriceAsRange($minAmount)
    {
        $incPriceRang = $this->getConfig('increment_option/price_range');

        $incPriceRang = @json_decode($incPriceRang, true);
        if ($incPriceRang) {
            foreach ($incPriceRang as $priceItem) {
                $from = isset($priceItem['from']) ? (float)$priceItem['from'] : 0;
                $to = isset($priceItem['to']) ? (float)$priceItem['to'] : 0;
                $increment = isset($priceItem['increment']) ? (float)$priceItem['increment'] : 0;
                if ($minAmount >= $from && $minAmount <= $to) {
                    return floatval($increment);
                }
            }
        }

        return false;
    }

    /**
     * get Active Auction Id
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
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getRates()
    {
        $store = $this->_storeManager->getStore();
        $currencyModel = $this->_dirCurrencyFactory->create();
        $baseCunyCode = $store->getBaseCurrencyCode();
        $cuntCunyCode = $store->getCurrentCurrencyCode();

        $allowedCurrencies = $currencyModel->getConfigAllowCurrencies();
        $rates = $currencyModel->getCurrencyRates(
            $baseCunyCode,
            array_values($allowedCurrencies)
        );
        $rates[$cuntCunyCode] = isset($rates[$cuntCunyCode]) ?
            $rates[$cuntCunyCode] : 1;
        return $rates[$cuntCunyCode];
    }

    /**
     * get total bids
     *
     * @param int $auctionId
     * @param int $customerId
     * @return int
     */
    public function getTotalBids($auctionId, $customerId)
    {
        $mixCollection = $this->_mixAmountCollectionFactory->create()
                                ->addFieldToFilter('auction_id', $auctionId)
                                ->addFieldToFilter('is_spam', 0)
                                ->addFieldToFilter('winning_status', 0)
                                ->addFieldToFilter('customer_id', ["neq" => $customerId]);

        return $mixCollection->count();
    }

    /**
     * @param $isAuto
     * @param $auctionData
     * @param $auctionConfig
     * @return bool
     */
    public function checkContinues($isAuto, $auctionData, $auctionConfig)
    {
        if (!$auctionConfig['restriction_enable']) {
            return true;
        }
        $customer_id = $this->_customerSession->getCustomerId();
        if(!$this->_customerSession->isLoggedIn() && !$customer_id){
            return false;
        }
        $today = $this->getTimezoneDateTime();
        $currentTime = strtotime($today);

        //check customer is spam for the auction
        $checkIsSpam = $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', $auctionData['entity_id'])
            ->addFieldToFilter('customer_id', (int)$customer_id)
            ->addFieldToFilter('winning_status', 0)
            ->addFieldToFilter('is_spam', 1);
        if($checkIsSpam->count()) {
            return false;
        }
        $mixCollection = $this->_mixAmountCollectionFactory->create()
                                ->addFieldToFilter('auction_id', $auctionData['entity_id'])
                                ->addFieldToFilter('is_spam', 0)
                                ->addFieldToFilter('winning_status', 0)
                                ->setOrder('updated_at','DESC');

        if($this->getConfig("restriction/enable_each_customer")){
            $mixCollection->addFieldToFilter("customer_id", (int)$customer_id);
        }
        if (!$mixCollection->count()) {
            return true;
        }
        $lastAmount =  $mixCollection->getFirstItem();

        $updatedAt = $lastAmount->getUpdatedAt();
        //$updatedAt = $this->getTimezoneDateTime($updatedAt);
        $lastUpdatedTime = strtotime($updatedAt);
        $restrictPeriod = $auctionConfig['restriction_period'];
        $restrictTimes = $auctionConfig['restriction_times'];
        if(isset($auctionData["continue_time"]) && $auctionData["continue_time"]){
            $restrictPeriod = $auctionData["continue_time"];
        }
        if(isset($auctionData["limit_bids"]) && $auctionData["limit_bids"]){
            $restrictTimes = $auctionData["limit_bids"];
        }
        $restrictCount = $lastAmount->getRestrictCount();

        if (($currentTime - $lastUpdatedTime)/60 < $restrictPeriod) {
            if ($restrictCount < $restrictTimes) {
                $restrictCount++;
                $lastAmount->setRestrictCount($restrictCount);
                $lastAmount->setUpdatedAt($currentTime);
                $lastAmount->save();
                return true;
            } else {
                return false;
            }
        } else {
            $lastAmount->setRestrictCount(1);
            $lastAmount->setUpdatedAt($currentTime);
            $lastAmount->save();
            return true;
        }
    }

    /**
     * @param $auctionId
     * @param $auctionConfig
     * @param bool $returnArray
     * @return float|mixed
     */
    public function getMinAmount($auctionId, $auctionConfig, $returnArray = false)
    {
        $biddingModel = $this->_auctionProductFactory->create()->load($auctionId);
        if ($biddingModel->getMinAmount() > $biddingModel->getStartingPrice()) {
            $minPrice = $biddingModel->getMinAmount();
        } else {
            $minPrice = $biddingModel->getStartingPrice();
        }
        $bidRecordLast = $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', ['eq' => $auctionId])
            ->addFieldToFilter('is_spam', ['eq' => '0'])
            ->setOrder('amount', 'DESC')
            ->getFirstItem();

        $customerId = 0;
        if ($bidRecordLast->getEntityId()) {
            $amount = $bidRecordLast->getAmount();
            $customerId = $bidRecordLast->getCustomerId();
        } else {
            $amount = $minPrice;
        }
        $step = $auctionConfig['step'];
        $incVal = 0;
        $increment_auc_enable = (int)$biddingModel->getIncrementOpt();
        if ($auctionConfig['increment_auc_enable'] && $increment_auc_enable) {
            $incVal = $this->getIncrementPriceAsRange($amount);
            $tmpVal = $incVal ? ((float)$amount + (float)$incVal) : ((float)$amount + (float)$step);
        } else {
            $tmpVal = (float)$amount + (float)$step;
        }
        if ($returnArray) {
            return [
                "minAmount" => number_format($tmpVal, 2),
                "customerId" => $customerId
            ];
        } else {
            return number_format($tmpVal, 2);
        }
    }

    /**
     * @param Object|mixed $biddingModel
     * @param array|mixed $auctionConfig
     * @param float|int $amount
     * @return float
     */
    public function convertAuctionMinAmount($biddingModel, $auctionConfig, $amount)
    {
        $step = $auctionConfig['step'];
        $incVal = 0;
        if ($biddingModel->getIncrementOpt() && $auctionConfig['increment_auc_enable']) {
            $incVal = $this->getIncrementPriceAsRange($amount);
            $tmpVal = $incVal ? ((float)$amount + (float)$incVal) : ((float)$amount + (float)$step);
        } else {
            $tmpVal = (float)$amount + (float)$step;
        }
        return $tmpVal;
    }

    /**
     * @param $auctionId
     * @param $customerId
     * @return bool|mixed
     */
    public function getCurrentAmount($auctionId, $customerId)
    {
        $customerId = $customerId ? $customerId : '';
        $auctionAmount = $this->_aucAmountFactory->create()
            ->getCollection()
            ->addFieldToFilter('auction_id', $auctionId)
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('is_spam', 0)
            ->getFirstItem();
        $amount = $auctionAmount->getAuctionAmount();

        $auctionAutoAmount = $this->_autoAuction->create()
            ->getCollection()
            ->addFieldToFilter('auction_id', $auctionId)
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('is_spam', 0)
            ->getFirstItem();
        $autoAmount = $auctionAutoAmount->getAmount();
        if (!$amount && !$autoAmount) {
            return false;
        } elseif ((!$amount && $autoAmount) || $autoAmount > $amount) {
            return $autoAmount;
        } elseif (!$autoAmount || $amount >= $autoAmount) {
            return $amount;
        }
    }

    /**
     * @param $auctionId
     * @return DataObject
     */
    public function getBestCurrentBid($auctionId)
    {
        return $this->historyCollection->create()
            ->addFieldToFilter('auction_id', $auctionId)
            ->addFieldToFilter('status', 1)
            ->setOrder('auction_amount')
            ->getFirstItem();
    }

    /**
     * @param $amount
     * @param $auctionId
     * @param $auctionConfig
     * @return bool
     */
    public function checkAmount($amount, $auctionId, $auctionConfig)
    {
        $val = $amount;
        $biddingModel = $this->_auctionProductFactory->create()->load($auctionId);
        $maxPrice = $biddingModel->getMaxAmount();
        if ($maxPrice && $maxPrice < $val) {
            return false;
        } else {
            $tmpVal = $this->getMinAmount($auctionId, $auctionConfig);
            if ($tmpVal > $val) {
                return false;
            }
        }
        return true;
    }

    /**
     * converToTz convert Datetime from one zone to another
     *
     * @param string $dateTime which we want to convert
     * @param string $toTz     timezone in which we want to convert
     * @param string $fromTz   timezone from which we want to convert
     * @return string
     * @throws Exception|Exception
     */
    public function convertToTz($dateTime = "", $toTz = '', $fromTz = '')
    {
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('Y-m-d H:i:s');

        return $dateTime;
    }

    /**
     * @return string
     */
    public function getCurrentTime()
    {
        return $this->_timezoneInterface->date()->format('Y-m-d H:i:s');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCurrentTimeUtc()
    {
        $today = $this->getCurrentTime();
        return $this->convertToTz(
            $today,
            $this->_timezoneInterface->getDefaultTimezone(),
            $this->_timezoneInterface->getConfigTimezone()
        );
    }

    /**
     * @param $time
     * @return string
     * @throws Exception
     */
    public function convertUtcToStoreTime($time)
    {
        return $this->convertToTz(
            $time,
            $this->_timezoneInterface->getConfigTimezone(),
            $this->_timezoneInterface->getDefaultTimezone()
        );
    }

    /**
     * @param $productId
     * @return DataObject
     */
    public function getAuctionByProduct($productId)
    {
        $collection = $this->_auctionProductFactory->create()->getCollection();
        return $collection->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS)
            ->getFirstItem();
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();;
    }

    /**
     * @param string|int $auctionId
     * @param string|null $pro_url
     * @return array|bool|mixed|null
     * @throws Exception
     */
    public function getAuctionDetail($auctionId, $pro_url = null)
    {
        $auctionConfig = $this->getAuctionConfiguration();
        $auctionData = false;
        $auction = $this->_auctionProductFactory->create()->load($auctionId);
        $currentProId = $auction->getProductId();
        $curPro = $this->getProductById($currentProId);
        //$status = $auction->getData('status');
        $auctionStatus = $auction->getData('auction_status');
        if ($auctionConfig['enable'] && in_array($auctionStatus, [AuctionStatus::STATUS_TIME_END, AuctionStatus::STATUS_PROCESS]) && $auction->getId()) {
            $auctionData = $auction->getData();
            $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $currentProId])
                ->addFieldToFilter('auction_id', ['eq' => $auctionData['entity_id']])
                ->addFieldToFilter('winning_status', ['eq' => 1])
                ->addFieldToFilter('shop', ['neq' => 0])->getFirstItem();

            if ($aucAmtData->getEntityId()) {
                $aucAmtData = $this->_autoAuction->create()->getCollection()
                    ->addFieldToFilter('auction_id', ['eq' => $auctionData['entity_id']])
                    ->addFieldToFilter('flag', ['eq' => 1])
                    ->addFieldToFilter('shop', ['neq' => 0])->getFirstItem();
            }
            $today = $this->getTimezoneDateTime();
            $auctionData['stop_auction_utc_time'] = $auctionData['stop_auction_time'];
            $auctionData['start_auction_utc_time'] = $auctionData['start_auction_time'];
            $auctionData['stop_auction_time'] = $this->getTimezoneDateTime($auctionData['stop_auction_time']);
            $auctionData['start_auction_time'] = $this->getTimezoneDateTime($auctionData['start_auction_time']);
            $auctionData['today_time'] = $today;
            $auctionData['current_time_stamp'] = strtotime($today);
            $auctionData['start_auction_time_stamp'] = strtotime($auctionData['start_auction_time']);
            $auctionData['stop_auction_time_stamp'] = strtotime($auctionData['stop_auction_time']);
            $auctionData['new_auction_start'] = $aucAmtData->getEntityId() ? true : false;
            $auctionData['auction_title'] = __('Bid on ') . $curPro->getName();
            $auctionData['pro_url'] = $pro_url ? $pro_url: ($this->_urlBuilder->getUrl() . $curPro->getUrlKey() . '.html');
            $auctionData['pro_name'] = $curPro->getName();
            $auctionData['max_bids'] = isset($auctionData['max_bids']) ? (int)$auctionData['max_bids'] : 0;
            $auctionData['pro_buy_it_now'] = $auctionData['buy_it_now'];
            $auctionData['pro_auction'] = $auctionData['auction_status'];
            if ($auctionData['min_amount'] < $auctionData['starting_price']) {
                $auctionData['min_amount'] = $auctionData['starting_price'];
            }
        }
        return $auctionData;
    }
}
