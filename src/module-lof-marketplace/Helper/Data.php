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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Validator\EmailAddress;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\Source\CalculateType;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_CATALOG_PRODUCT_TYPE_RESTRICTION = 'vendors/catalog/product_type_restriction';
    const XML_CATALOG_ATTRIBUTE_SET_RESTRICTION = 'vendors/catalog/attribute_set_restriction';

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
        array $modulesUseTemplateFromAdminhtml = [],
        array $blocksUseTemplateFromAdminhtml = []
    ) {
        parent::__construct($context);
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
    }

    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('buyer-seller');
            $this->getDataPersistor()->clear('buyer-seller');
        }

        if (isset($this->postData[$key])) {
            return $this->postData[$key];
        }

        return '';
    }

    /**
     * Get Data Persistor
     *
     * @return \Magento\Framework\App\Request\DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\App\Request\DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }

    /**
     * Upload Images Zip File
     *
     * @param array $result
     *
     * @return array
     */
    public function uploadZip($strtotime)
    {
        $profileId = $strtotime;
        try {
            $zipModel = $this->_zip;
            $basePath = $this->getBasePath($profileId);
            $imageUploadPath = $basePath . 'zip/';
            $imageUploader = $this->_fileUploader->create(['fileId' => 'massupload_image']);
            $imageUploader->validateFile();
            $imageUploader->setAllowedExtensions(['zip']);
            $imageUploader->setAllowRenameFiles(true);
            $imageUploader->setFilesDispersion(false);
            $imageUploader->save($imageUploadPath);
            $fileName = $imageUploader->getUploadedFileName();
            $source = $imageUploadPath . $fileName;
            $destination = $basePath . 'images';

            $zipModel->unzipImages($source, $destination);
            $this->arrangeFiles($destination);

            $this->flushFilesCache($destination);
            $result = ['error' => false];
        } catch (\Exception $e) {
            $this->flushData($profileId);
            $msg = 'There is some problem in uploading image zip file.';
            $result = ['error' => true, 'msg' => $msg];
        }
        return $result;
    }

    /**
     * @param $path
     * @param bool $removeParent
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function flushFilesCache($path, $removeParent = false)
    {
        $entries = $this->_fileDriver->readDirectory($path);
        foreach ($entries as $entry) {
            if ($this->_fileDriver->isDirectory($entry)) {
                $this->removeDir($entry);
            }
        }
        if ($removeParent) {
            $this->removeDir($path);
        }
    }

    /**
     * @param $dir
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function removeDir($dir)
    {
        if ($this->_fileDriver->isDirectory($dir)) {
            $entries = $this->_fileDriver->readDirectory($dir);
            foreach ($entries as $entry) {
                if ($this->_fileDriver->isFile($entry)) {
                    $this->_fileDriver->deleteFile($entry);
                } else {
                    $this->removeDir($entry);
                }
            }
            $this->_fileDriver->deleteDirectory($dir);
        }
    }

    /**
     * @param $profileId
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
     */
    public function flushData($profileId)
    {
        /* $this->_profile->create()->load($profileId)->delete();
         $path = $this->getBasePath($profileId);
         $this->flushFilesCache($path, true);*/
    }

    /**
     * @param $profileId
     * @return string
     */
    public function getBasePath($profileId)
    {
        $mediaPath = $this->getMediaPath();
        $basePath = $mediaPath . 'marketplace/massupload/' . $profileId . "/";
        return $basePath;
    }

    /**
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
    }

    /**
     * @param $path
     * @param string $originalPath
     * @param array $result
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function arrangeFiles($path, $originalPath = '', $result = [])
    {
        if ($originalPath == '') {
            $originalPath = $path;
        }

        $entries = $this->_fileDriver->readDirectory($path);

        foreach ($entries as $file) {
            $tmp = explode('/', $file);
            $fileName = end($tmp);
            $sourcePath = $path . '/' . $fileName;
            $destinationPath = $originalPath . '/' . $fileName;
            $path1 = $this->getMediaPath() . 'tmp/catalog/product/'
                . strtolower($fileName[0]) . '/' . strtolower($fileName[1]);
            $lof_path = $path1 . '/' . strtolower($fileName);

            if ($this->_fileDriver->isDirectory($file)) {
                $result = $this->arrangeFiles($file, $originalPath, $result);
            } else {
                if (!$this->_fileDriver->isExists($destinationPath)) {
                    $result[$sourcePath] = $destinationPath;
                    $this->_fileDriver->copy($sourcePath, $destinationPath);
                }
            }
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            if (!is_dir('pub/media/tmp/catalog/product/' . strtolower($fileName[0]) . '/' . strtolower($fileName[1]))) {
                $mkdir = 'pub/media/tmp/catalog/product/' . strtolower($fileName[0]) . '/' . strtolower($fileName[1]);
                // phpcs:ignore Magento2.Functions.DiscouragedFunction
                mkdir($mkdir, 0777, true);
            }
            $this->_fileDriver->copy($sourcePath, $lof_path);
            $file = '/' . strtolower($fileName[0])
                . '/' . strtolower($fileName[1])
                . '/' . strtolower($fileName) . '.tmp';
            $this->uploadimage->moveImageFromTmp($file);
        }
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * @param $countryCode
     * @return string
     */
    public function getCountryname($countryCode)
    {
        try {
            $country = $this->_countryFactory->create()->loadByCode($countryCode);
            return $country->getName();
        } catch (\Exception $e) {
            return $countryCode;
        }
    }

    /**
     * @param $module
     * @return bool
     */
    public function isEnableModule($module)
    {
        return $this->_moduleManager->isEnabled($module);
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * Get current currency symbol
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencySymbol();
    }

    /**
     * @param float $price
     * @param string $currencyCode
     * @param int $scale
     * @return string
     */
    public function getPriceFomat($price, $currencyCode = '', $scale = 2)
    {
        if (!$currencyCode) {
            $currencyCode = $this->getCurrentCurrencyCode();
        }
        return $this->priceFormatter->format(
            $price,
            false,
            $scale,
            null,
            $currencyCode
        );
    }

    /**
     * @param float $amount
     * @return string
     */
    public function getFormatedPrice($amount)
    {
        return $this->_priceCurrency->convertAndFormat($amount);
    }

    /**
     * @return array
     */
    public function getGroupList()
    {
        $result = [];
        $collection = $this->_groupCollection->getCollection()->addFieldToFilter('status', \Lof\MarketPlace\Model\Group::STATUS_ENABLED);
        foreach ($collection as $sellerGroup) {
            $result[$sellerGroup->getGroupId()] = $sellerGroup->getName();
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getCustomerOfSeller()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $seller = $objectManager->get(\Lof\MarketPlace\Model\ResourceModel\Seller\Collection::class);
        $customer_id = [];
        foreach ($seller as $_seller) {
            $customer_id[] = $_seller->getCustomerId();
        }
        return $customer_id;
    }

    /**
     * @param null $customer_id
     * @return array
     */
    public function getCustomerList($customer_id = null)
    {
        $result = [];

        $collection = $this->customer->create()->getCollection();
        $result['0'] = __("Enter new email address");
        foreach ($collection as $customer) {
            if (!in_array($customer->getId(), $this->getCustomerOfSeller()) || $customer_id == $customer->getId()) {
                $result[$customer->getId()] = $customer->getName() . '(' . $customer->getEmail() . ')';
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getCommissionList()
    {
        $result = [];
        $collection = $this->_commissionCollection->getCollection()->addFieldToFilter('status', '1');
        foreach ($collection as $sellerGroup) {
            $result[$sellerGroup->getId()] = $sellerGroup->getCommissionTitle();
        }
        return $result;
    }

    /**
     * @param $sellerId
     * @param $productId
     * @return int|string
     */
    public function getCommission($sellerId, $productId)
    {
        $commission = 100;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerProduct = $objectManager->get(\Lof\MarketPlace\Model\SellerProduct::class)
            ->load($productId, 'product_id');
        $sellerCommission = $this->dataRule->getRuleProducts($sellerId, $productId);
        $productCommission = $sellerProduct->getCommission();
        $configCommission = $this->getConfig('seller_settings/commission');
        if (!empty($productCommission) && $productCommission > 0) {
            $commission = $productCommission;
        } elseif ($sellerCommission) {
            $commission = $sellerCommission->getData();
        } elseif (!empty($configCommission) && is_numeric($configCommission)) {
            $commission = $configCommission;
        }
        return $commission;
    }

    /**
     * @param $sellerId
     * @return int|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShippingCommission($sellerId)
    {
        $commission = 100;
        $sellerCommission = $this->dataRule->getRuleShippingCommission($sellerId);
        $configCommission = $this->getConfig('seller_settings/shipping_commission');
        if ($sellerCommission) {
            $commission = $sellerCommission->getData();
        } elseif (!empty($configCommission) && is_numeric($configCommission)) {
            $commission = $configCommission;
        }
        return $commission;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCommissionType()
    {
        $allowedTypes = [
            \Lof\MarketPlace\Model\Source\CommissionType::TYPE_PRODUCT,
            \Lof\MarketPlace\Model\Source\CommissionType::TYPE_PRODUCT_SHIPPING,
        ];

        $configCommission = $this->getConfig('seller_settings/commission_type');
        return $configCommission && in_array($configCommission, $allowedTypes) ? (int)$configCommission :  1;
    }

    /**
     * is enable calculate commission for parent item in cart or not
     *
     * @param mixed|null $store
     * @return int|bool
     */
    public function calculateCommissionForParent($store = null)
    {
        $calculate_parent_only = $this->getConfig("seller_settings/calculate_parent_only", $store);
        $calculate_parent_only = $calculate_parent_only != null || $calculate_parent_only != "" ? (int)$calculate_parent_only : 1;
        return $calculate_parent_only;
    }

    /**
     * Verify calculate commission for sales item or quote item
     *
     * @param mixed $item
     * @param int|null $sellerId
     * @param mixed|null $store
     * @return bool
     */
    public function verifyCalculateCommission($item, $sellerId, $store = null)
    {
        $flag = false;
        $calculateParentOnly = $this->calculateCommissionForParent($store);
        $checkedCalculateTypeParent = (($calculateParentOnly == CalculateType::TYPE_PARENT_ONLY || $calculateParentOnly == CalculateType::TYPE_BOTH) && $item->getParentItemId() == '') ? true : false;
        $checkedCalculateTypeChild = ((!$calculateParentOnly || $calculateParentOnly == CalculateType::TYPE_BOTH) && $item->getParentItemId()) ? true : false;

        if (!empty($sellerId) && ($checkedCalculateTypeParent || $checkedCalculateTypeChild)) {
            $flag = true;
        }
        return $flag;
    }

    /**
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigShippingCommission()
    {
        $configCommission = $this->getConfig('seller_settings/shipping_commission');
        return $configCommission ? (float)$configCommission :  100;
    }

    /**
     * @param $productId
     * @param $commissionId
     * @return bool
     */
    protected function _validateProductConsition($productId, $commissionId)
    {
        if (isset($productId)) {
            if (!$this->dataRule->getRuleProducts($productId, $commissionId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get seller ID of current customer
     * @return mixed
     */
    public function getSellerId()
    {
        $seller = $this->getSellerByCustomerId($this->getCustomerId());
        return $seller ? $seller->getData('seller_id') : 0;
    }

    /**
     * getSellerByCustomerId
     *
     * @param int $customer_id
     * @return mixed
     */
    public function getSellerByCustomerId($customer_id)
    {
        if (!isset($this->_seller_by_customers[$customer_id])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_seller_by_customers[$customer_id] = $objectManager->create(Seller::class)
                ->load($customer_id, 'customer_id');
        }
        return $this->_seller_by_customers[$customer_id];
    }

    /**
     * getSellerById
     *
     * @param int $seller_id
     * @return mixed
     */
    public function getSellerById($seller_id)
    {
        if (!isset($this->_sellers[$seller_id])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_sellers[$seller_id] = $objectManager->create(Seller::class)->load($seller_id);
        }
        return $this->_sellers[$seller_id];
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore(true)->getWebsite()->getId();
    }

    /**
     * @return false|string[]
     */
    public function getProductTypeRestriction()
    {
        return explode(",", $this->scopeConfig->getValue(self::XML_CATALOG_PRODUCT_TYPE_RESTRICTION));
    }

    /**
     * @return false|string[]
     */
    public function getAttributeSetRestriction()
    {
        return explode(",", $this->scopeConfig->getValue(self::XML_CATALOG_ATTRIBUTE_SET_RESTRICTION));
    }

    /**
     * @return bool|string|null
     */
    public function getUrlShortcut()
    {
        if ($this->getConfig('general_settings/route')) {
            return $this->getConfig('general_settings/route');
        }
        return false;
    }

    /**
     * @param $key
     * @param null $store
     * @param string $section
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($key, $store = null, $section = "lofmarketplace")
    {
        $store = $this->_storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            $section . '/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @param $key
     * @param null $store
     * @param string $section
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigCustomer($key, $store = null, $section = "customer")
    {
        $store = $this->_storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            $section . '/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @param $key
     * @param null $store
     * @param string $section
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfigPaypal($key, $store = null, $section = "payment")
    {
        $store = $this->_storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            $section . '/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @param $key
     * @param null $store
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * @param $str
     * @return string
     * @throws \Exception
     */
    public function filter($str)
    {
        if ($str == null){
            $html = '';
        }else{
            $html = $this->_filterProvider->getPageFilter()->filter($str);
        }
        return $html;
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getName();
        return $customer->getData('firstname') . ' ' . $customer->getData('lastname');
    }

    /**
     * getCurrentCustomer
     *
     * @return mixed|array|null
     */
    public function getCurrentCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @return array|mixed|null
     */
    public function getCustomerEmail()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getData('email');
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        $customer = $this->customerSession->getCustomer();

        return $customer->getId();
    }

    /**
     * @param $timestamp
     * @param int $detailLevel
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function nicetime($timestamp, $detailLevel = 1)
    {
        $periods = ['sec', 'min', 'hour', 'day', 'week', 'month', 'year', 'decade'];
        $lengths = ['60', '60', '24', '7', '4.35', '12', '10'];

        $now = time();
        $timestamp = strtotime($timestamp);
        // check validity of date
        if (empty($timestamp)) {
            return 'Unknown time';
        }

        // is it future date or past date
        if ($now > $timestamp) {
            $difference = $now - $timestamp;
            $tense = 'ago';
        } else {
            $difference = $timestamp - $now;
            $tense = 'from now';
        }

        if ($difference == 0) {
            return '1 sec ago';
        }

        $remainders = [];
        $count = count($lengths);
        for ($j = 0; $j < $count; ++$j) {
            $remainders[$j] = floor(fmod($difference, $lengths[$j]));
            $difference = floor($difference / $lengths[$j]);
        }

        $difference = round($difference);
        $remainders[] = $difference;
        $string = '';

        for ($i = count($remainders) - 1; $i >= 0; --$i) {
            if ($remainders[$i]) {
                // on last detail level get next period and round current
                if ($detailLevel == 1 && isset($remainders[$i - 1]) && $remainders[$i - 1] > $lengths[$i - 1] / 2) {
                    $remainders[$i]++;
                }
                $string .= $remainders[$i] . ' ' . $periods[$i];

                if ($remainders[$i] != 1) {
                    $string .= 's';
                }

                $string .= ' ';

                --$detailLevel;

                if ($detailLevel <= 0) {
                    break;
                }
            }
        }

        return $string . $tense;
    }

    /**
     * @param $date
     * @param string $type
     * @return string
     */
    public function getFormatDate($date, $type = 'full')
    {
        $result = '';
        switch ($type) {
            case 'full':
                $result = $this->formatDate($date, \IntlDateFormatter::FULL);
                break;
            case 'long':
                $result = $this->formatDate($date, \IntlDateFormatter::LONG);
                break;
            case 'medium':
                $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
                break;
            case 'short':
                $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
                break;
        }
        return $result;
    }

    /**
     * @param null $date
     * @param int $format
     * @param bool $showTime
     * @param null $timezone
     * @return string
     * @throws \Exception
     */
    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
    ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
        );
    }

    /**
     * @param $id
     * @return \Magento\Framework\Phrase|string
     */
    public function getStatus($id)
    {
        $data = '';
        if ($id == 0) {
            $data = __('Close');
        } elseif ($id == 1) {
            $data = __('Open');
        } elseif ($id == 2) {
            $data = __('Processing');
        } elseif ($id == 3) {
            $data = __('Done');
        }

        return $data;
    }

    /**
     * @param $id
     * @return \Magento\Framework\Phrase|string
     */
    public function getStatusRating($id)
    {
        $data = '';
        if ($id == 1) {
            $data = __('Approved');
        } elseif ($id == 2) {
            $data = __('Pending');
        } elseif ($id == 3) {
            $data = __('Not Approved');
        }

        return $data;
    }

    /**
     * @return array
     */
    public function arrayStatusRating()
    {
        $data = [];
        $data[] = [];

        $data[0]['value'] = 'reject';
        $data[0]['label'] = __('Reject');

        $data[1]['value'] = 'pending';
        $data[1]['label'] = __('Pending');

        $data[2]['value'] = 'accept';
        $data[2]['label'] = __('Accept');

        return $data;
    }

    /**
     * @return array
     */
    public function statusRating()
    {
        $data = [];

        $data['reject'] = __('Reject');
        $data['pending'] = __('Pending');
        $data['accept'] = __('Accept');

        return $data;
    }

    /**
     * @return array
     */
    public function arrayStatus()
    {
        $data = [];
        $data[] = [];

        $data[0]['value'] = 0;
        $data[0]['label'] = __('Close');

        $data[1]['value'] = 1;
        $data[1]['label'] = __('Open');

        $data[2]['value'] = 2;
        $data[2]['label'] = __('Processing');

        $data[3]['value'] = 3;
        $data[3]['label'] = __('Done');

        return $data;
    }

    /**
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getProduct()
    {
        $collection = $this->productCollectionFactory->create()->load();
        return $collection;
    }

    /**
     * @param $product_id
     * @return \Magento\Catalog\Model\Product|\Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getProductById($product_id)
    {
        $collection = $this->productCollectionFactory->create()->load($product_id);
        return $collection;
    }

    /**
     * @param $customer_id
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomerById($customer_id)
    {
        $collection = $this->customer->create()->load($customer_id);
        return $collection;
    }

    /**
     * @param $customer_email
     * @return \Magento\Framework\DataObject
     */
    public function getCustomerByEmail($customer_email)
    {
        return $this->customer->create()->getCollection()
            ->addFieldToFilter('email', $customer_email)
            ->getFirstItem();
    }

    /**
     * @param bool $checkHost
     * @return bool|string
     */
    public function getAreaFrontName($checkHost = false)
    {
        return $this->_frontNameResolver->getFrontName($checkHost);
    }

    /**
     * @return mixed
     */
    public function getMediaUrl()
    {
        return $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @return string
     */
    public function getCurrentUrls()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * @param bool $fromStore
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getCurrentUrl($fromStore);
    }

    /**
     * @param bool $fromStore
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseStoreUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isStoreActive()
    {
        return $this->_storeManager->getStore()->isActive();
    }

    /**
     * @return array
     */
    public function getModulesUseTemplateFromAdminhtml()
    {
        return $this->_modulesUseTemplateFromAdminhtml;
    }

    /**
     * @return array
     */
    public function getBlocksUseTemplateFromAdminhtml()
    {
        return $this->_blocksUseTemplateFromAdminhtml;
    }

    /**
     * @param $key
     * @return string
     */
    public function getTableKey($key)
    {
        $resource = $this->_objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $tablePrefix = (string)$this->_objectManager->get(\Magento\Framework\App\DeploymentConfig::class)
            ->get(\Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX);
        $exists = $resource->getConnection('core_write')->showTableStatus($tablePrefix . 'permission_variable');
        if ($exists) {
            return $key;
        } else {
            return "{$key}";
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getOrderinfo($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerOrder = $objectManager->create(\Lof\MarketPlace\Model\Order::class)->getCollection()
            ->addFieldToFilter('order_id', $id)
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->getFirstItem();
        return $sellerOrder;
    }

    /**
     * @param $orderId
     * @return mixed
     */
    public function getTracking($orderId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
        foreach ($order->getTracksCollection() as $tracking) {
            return $tracking;
        }

        return false;
    }

    /**
     * @param $order
     * @param $sellerId
     * @return bool|int
     */
    public function cancelorder($order, $sellerId)
    {
        $flag = 0;
        if ($order->canCancel()) {
            $order->getPayment()->cancel();
            $flag = $this->mpregisterCancellation($order, $sellerId);
        }

        return $flag;
    }

    /**
     * @param $order
     * @param $sellerId
     * @param string $comment
     * @return int
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function mpregisterCancellation($order, $sellerId, $comment = '')
    {
        $flag = 0;
        if ($order->canCancel()) {
            $cancelState = 'canceled';
            $items = [];
            $orderId = $order->getId();
            $trackingsdata = $this->_objectManager->create(\Lof\MarketPlace\Model\Orderitems::class)
                ->getCollection()
                ->addFieldToFilter(
                    'order_id',
                    $orderId
                )
                ->addFieldToFilter(
                    'seller_id',
                    $sellerId
                );

            foreach ($trackingsdata as $tracking) {
                $items[] = $tracking->getData('product_id');
            }

            foreach ($trackingsdata as $tracking) {
                $itemsarray = $this->_getItemQtys($order, $items);
                foreach ($order->getAllItems() as $item) {
                    if (in_array($item->getProductId(), $items)) {
                        $flag = 1;
                        $item->cancel();
                    }
                }
                foreach ($order->getAllItems() as $item) {
                    if ($cancelState != 'processing' && $item->getQtyToRefund()) {
                        if ($item->getQtyToShip() > $item->getQtyToCancel()) {
                            $cancelState = 'processing';
                        } else {
                            $cancelState = 'complete';
                        }
                    } elseif ($item->getQtyToInvoice()) {
                        $cancelState = 'processing';
                    }
                }
                $order->setState($cancelState, true, $comment)->save();
            }
        }

        return $flag;
    }

    /**
     * @param $order
     * @param $items
     * @return array
     */
    protected function _getItemQtys($order, $items)
    {
        $data = [];
        $subtotal = 0;
        $baseSubtotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getProductId(), $items)) {
                $data[$item->getItemId()] = (int)$item->getQtyOrdered();
                $subtotal += $item->getRowTotal();
                $baseSubtotal += $item->getBaseRowTotal();
            } else {
                $data[$item->getItemId()] = 0;
            }
        }

        return [
            'data' => $data,
            'subtotal' => $subtotal,
            'basesubtotal' => $baseSubtotal,
        ];
    }

    /**
     * @param bool $flag
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function checkAuth($flag = true)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        $header = getallheaders();
        if (isset($header['Authorization'])) {
            $base_url = $this->_storeManager->getStore()->getBaseUrl() . 'rest/V1/customers/me';
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $ch = curl_init();
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            curl_setopt($ch, CURLOPT_URL, $base_url);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $header = [
                'authorization: ' . $header['Authorization']
            ];
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $server_output = curl_exec($ch);
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            curl_close($ch);
            if ($server_output && $flag) {
                $sellerFactory = $this->_objectManager->create(\Lof\MarketPlace\Model\SellerFactory::class);
                $customerId = $this->getAPISellerID($server_output);
                $status = $sellerFactory->create()->load($customerId, 'customer_id')->getStatus();
                return $server_output;
            } else {
                return $server_output;
            }
        }
        return '';
    }

    /**
     * @param string $seller_data
     * @return mixed
     */
    public function getAPISellerID($seller_data = '')
    {
        if ($seller_data != '') {
            $seller_data = json_decode($seller_data);
            $seller_id = $seller_data['id'];
            return $seller_id;
        }
        return false;
    }

    /**
     * @param $section
     * @param $groups
     * @param $seller_id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveShippingData($section, $groups, $seller_id)
    {
        foreach ($groups as $code => $values) {
            if (is_array($values) && count($values) > 0) {
                foreach ($values as $name => $value) {
                    $serialized = 0;
                    $key = strtolower($section . '/' . $code . '/' . $name);
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $key_tmp = $this->getTableKey('key');
                    $seller_id_tmp = $this->getTableKey('seller_id');
                    $setting = $this->_objectManager->get(\Lof\MarketPlace\Model\ConfigFactory::class)->create()
                        ->loadByField([$key_tmp, $seller_id_tmp], [$key, $seller_id]);
                    if ($setting && $setting->getId()) {
                        $setting->setSellerId($seller_id)
                            ->setGroup($section)
                            ->setKey($key)
                            ->setValue($value)
                            ->setSerialized($serialized)
                            ->save();
                    } else {
                        $setting = $this->_objectManager->get(\Lof\MarketPlace\Model\ConfigFactory::class)->create();
                        $setting->setSellerId($seller_id)
                            ->setGroup($section)
                            ->setKey($key)
                            ->setValue($value)
                            ->setSerialized($serialized)
                            ->save();
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getAllowedSocials()
    {
        $configAllowed = $this->getConfig('seller_settings/allowed_socials');

        if (!$configAllowed) {
            return [];
        }

        return explode(',', $configAllowed);
    }

    /**
     * @param $social
     * @return bool
     */
    public function isAllowedSocial($social)
    {
        return in_array($social, $this->getAllowedSocials());
    }

    /**
     * Validate email address is valid
     *
     * @param string $value
     * @return bool
     */
    public function validateEmailAddress($value)
    {
        $validator = new EmailAddress();
        $validator->setMessage(
            __('"%1" invalid type entered.', $value),
            \Zend_Validate_EmailAddress::INVALID
        );
        $phpValidateEmail = filter_var($value, FILTER_VALIDATE_EMAIL);
        $coreValidateEmail = true;
        if (!$validator->isValid($value)) {
            $coreValidateEmail = false;
        }

        return $phpValidateEmail && $coreValidateEmail;
    }

    /**
     * get seller address
     *
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @return mixed|array
     */
    public function getSellerAddress($seller)
    {
        return [
            'country_id' => $seller->getCountry(),
            'city' => $seller->getCity(),
            'postcode' => $seller->getPostcode(),
            'region_id' => $seller->getRegionId(),
            'region' => $seller->getRegion()
        ];
    }

    /**
     * @param $data_array
     * @return array
     */
    public function xss_clean_array($data_array)
    {
        $result = [];
        if (is_array($data_array)) {
            foreach ($data_array as $key => $val) {
                $val = $this->xss_clean($val);
                $result[$key] = $val;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return string|string[]|null
     */
    public function xss_clean($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        // Fix &entity\n;
        $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $data
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $data
        );

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
                '',
                $data
            );
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
}
