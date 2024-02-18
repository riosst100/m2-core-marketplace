<?php

namespace CoreMarketplace\Auction\Helper;

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
use Lof\MarketPlace\Helper\Data as MarketplaceHelper;

class Data extends \Lofmp\Auction\Helper\Data
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
     * @var MarketplaceHelper
     */
    protected $mpHelper;

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
     * @param   MarketplaceHelper $mpHelper
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
        FormatInterface $localeFormat,
        MarketplaceHelper $mpHelper
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
        $this->mpHelper = $mpHelper;
        parent::__construct(
            $context,
            $dateTime,
            $timezoneInterface,
            $priceHelper,
            $auctionProFactory,
            $aucAmountFactory,
            $autoAuction,
            $filterProvider,
            $auctionAmount,
            $storeManager,
            $productFactory,
            $customer,
            $priceFormatter,
            $winnerData,
            $auctionProductFactory,
            $customerSession,
            $dirCurrencyFactory,
            $mixAmountCollectionFactory,
            $historyCollection,
            $localeFormat
        );
    }

    public function isEnabledForSeller()
    {
        if ($this->getConfig('general_settings/enable') && $this->getConfig('general_settings/allow_seller_manage')) {
            if ($seller = $this->mpHelper->getCurrentSeller()) {
                if ($group = $seller->getGroup()) {
                    if (isset($group['use_auction']) && $group['use_auction'] == 1) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}