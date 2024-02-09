<?php

namespace CoreMarketplace\MarketPlace\Helper;

use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Validator\EmailAddress;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\Source\CalculateType;

class Data extends \Lof\MarketPlace\Helper\Data
{
    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_groupCollection;

    /**
     * @var \Lof\MarketPlace\Model\Commission
     */
    protected $_commissionCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_config = [];

    /**
     * @var array
     */
    protected $_seller_by_customers = [];

    /**
     * @var array
     */
    protected $_sellers = [];

    /**
     * @var
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Lof\MarketPlace\App\Area\FrontNameResolver
     */
    protected $_frontNameResolver;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var
     */
    protected $localeDate;

    /**
     * @var DataRule
     */
    protected $dataRule;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customer;

    /**
     * @var array
     */
    protected $_blocksUseTemplateFromAdminhtml;

    /**
     * @var array
     */
    protected $_modulesUseTemplateFromAdminhtml;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Lof\MarketPlace\Model\Zip
     */
    protected $_zip;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_fileDriver;

    /**
     * @var
     */
    protected $_fileSystem;

    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var Uploadimage
     */
    protected $uploadimage;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $_localeDate;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $filesystemIo;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    /**
     * @var array
     */
    private $postData = null;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Lof\MarketPlace\Model\Group $groupCollection
     * @param \Lof\MarketPlace\Model\Commission $commissionCollection
     * @param DataRule $dataRule
     * @param \Lof\MarketPlace\Model\Zip $zip
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Lof\MarketPlace\App\Area\FrontNameResolver $frontNameResolver
     * @param \Magento\Catalog\Model\ProductFactory $productCollectionFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param PriceCurrencyInterface $priceFormatter
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Customer\Model\CustomerFactory $customer
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Filesystem\Io\File $filesystemIo
     * @param DirectoryList $directoryList
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param Uploadimage $uploadimage
     * @param \Magento\Framework\App\Helper\Context $context
     * @param RegionFactory $regionFactory
     * @param \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository
     * @param array $modulesUseTemplateFromAdminhtml
     * @param array $blocksUseTemplateFromAdminhtml
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\MarketPlace\Model\Group $groupCollection,
        \Lof\MarketPlace\Model\Commission $commissionCollection,
        \Lof\MarketPlace\Helper\DataRule $dataRule,
        \Lof\MarketPlace\Model\Zip $zip,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Lof\MarketPlace\App\Area\FrontNameResolver $frontNameResolver,
        \Magento\Catalog\Model\ProductFactory $productCollectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        PriceCurrencyInterface $priceFormatter,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filesystem\Io\File $filesystemIo,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Lof\MarketPlace\Helper\Uploadimage $uploadimage,
        RegionFactory $regionFactory,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        array $modulesUseTemplateFromAdminhtml = [],
        array $blocksUseTemplateFromAdminhtml = []
    ) {
        parent::__construct(
            $context,
            $storeManager,
            $groupCollection,
            $commissionCollection,
            $dataRule,
            $zip,
            $filesystem,
            $filterProvider,
            $frontNameResolver,
            $productCollectionFactory,
            $objectManager,
            $priceCurrency,
            $localeDate,
            $fileUploaderFactory,
            $fileDriver,
            $priceFormatter,
            $frontendUrl,
            $customer,
            $customerSession,
            $filesystemIo,
            $directoryList,
            $countryFactory,
            $dataPersistor,
            $uploadimage,
            $modulesUseTemplateFromAdminhtml,
            $blocksUseTemplateFromAdminhtml
        );
        $this->_directoryList = $directoryList;
        $this->uploadimage = $uploadimage;
        $this->_frontendUrl = $frontendUrl;
        $this->_countryFactory = $countryFactory;
        $this->dataPersistor = $dataPersistor;
        $this->_moduleManager = $context->getModuleManager();
        $this->_fileDriver = $fileDriver;
        $this->dataRule = $dataRule;
        $this->customer = $customer;
        $this->_localeDate = $localeDate;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_filterProvider = $filterProvider;
        $this->_storeManager = $storeManager;
        $this->_groupCollection = $groupCollection;
        $this->_commissionCollection = $commissionCollection;
        $this->customerSession = $customerSession;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_frontNameResolver = $frontNameResolver;
        $this->_objectManager = $objectManager;
        $this->_priceCurrency = $priceCurrency;
        $this->priceFormatter = $priceFormatter;
        $this->filesystemIo = $filesystemIo;
        $this->_blocksUseTemplateFromAdminhtml = $blocksUseTemplateFromAdminhtml;
        $this->_modulesUseTemplateFromAdminhtml = $modulesUseTemplateFromAdminhtml;
        $this->_zip = $zip;
        $this->_filesystem = $filesystem;
        $this->regionFactory = $regionFactory;
        $this->websiteRepository = $websiteRepository;
    }

    public function getPwaBaseUrl() 
    {
        return $this->getConfig('pwa/base_url') ?? '/';
    }

    public function getRegionData($regionCode)
    {
        $regionColl = $this->regionFactory->create()
        ->getCollection()
        ->addFieldToFilter('code', $regionCode);
        if ($regionColl->getSize()) {
            return $regionColl->getFirstItem();
        }

        return null;
    }

    public function setStoreBySellerCountry($sellerCountryID = null) 
    {
        $selectedWebsiteCode = $sellerCountryID ?? 'base';

        $storeId = null;
        $websiteId = $this->getWebsiteIdByCode($selectedWebsiteCode);
        if ($websiteId) {
            $storeId = $this->_storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        }
        
        if ($storeId) {
            $this->_storeManager->setCurrentStore($storeId);
        }

        return true;
    }

    public function getWebsiteIdByCode($code)
    {
        $websiteId = null;

        try {
            $website = $this->websiteRepository->get($code);
            $websiteId = (int)$website->getId();
        } catch (\Exception $exception) {}

        return $websiteId;
    }
}