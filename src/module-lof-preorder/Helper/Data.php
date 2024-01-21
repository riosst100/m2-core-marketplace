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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Products;
use Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory as Items;
use Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory;

/**
 * Class Data
 *
 * @package Lof\PreOrder\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_product;
    /**
     * @var Products
     */
    protected $_productCollection;

    public $_resource;

    /**
     * @var Configurable
     */
    protected $_configurable;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_catalogProduct;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var Items
     */
    protected $_itemCollection;
    /**
     * @var CollectionFactory
     */
    protected $_preorderCollection;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                          $context
     * @param \Magento\Cms\Model\Template\FilterProvider                     $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface                    $localeCurrency
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $localeDate
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param \Magento\Checkout\Model\Session                                $checkoutSession
     * @param \Magento\Customer\Model\CustomerFactory                        $customer
     * @param \Magento\Framework\Registry                                    $coreRegistry
     * @param \Magento\Framework\Mail\Template\TransportBuilder              $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface             $inlineTranslation
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable   $configurable
     * @param \Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory       $itemCollection
     * @param \Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory   $preorderCollection
     * @param \Magento\Sales\Model\OrderFactory                              $order
     * @param \Magento\Framework\App\ResourceConnection                      $resource
     * @param \Magento\Catalog\Model\ProductFactory                          $product
     * @param \Magento\Framework\Filter\FilterManager                        $filterManager
     * @param \Psr\Log\LoggerInterface                                       $logger
     * @param \Magento\Framework\Json\EncoderInterface                       $jsonEncoder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Framework\Registry $coreRegistry,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        Products $productCollection,
        Configurable $configurable,
        Items $itemCollection,
        CollectionFactory $preorderCollection,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder
    ) {
        parent::__construct($context);
        $this->_urlBuilder         = $context->getUrlBuilder();
        $this->_filterProvider     = $filterProvider;
        $this->_storeManager       = $storeManager;
        $this->_localeDate         = $localeDate;
        $this->_localeCurrency     = $localeCurrency;
        $this->_objectManager      = $objectManager;
        $this->customerSession     = $customerSession;
        $this->checkoutSession     = $checkoutSession;
        $this->coreRegistry        = $coreRegistry;
        $this->filterManager       = $filterManager;
        $this->_configurable       = $configurable;
        $this->_productCollection  = $productCollection;
        $this->_catalogProduct     = $product->create();
        $this->_resource           = $resource;
        $this->_product            = $product;
        $this->_order              = $order;
        $this->_transportBuilder   = $transportBuilder;
        $this->_inlineTranslation  = $inlineTranslation;
        $this->customer            = $customer;
        $this->_preorderCollection = $preorderCollection;
        $this->_itemCollection     = $itemCollection;
        $this->logger              = $logger;
        $this->jsonEncoder         = $jsonEncoder;
    }

    public function getJsonEncode($data)
    {
        return $this->jsonEncoder->encode($data);
    }

    public function getProductFactory()
    {
        return $this->_product;
    }

    public function getProductBySku($sku)
    {
        $product = $this->_product->create();

        return $product->loadByAttribute('sku', $sku);
    }

    /**
     * Get All Website Ids.
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        $websiteIds = [];
        $websites   = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }

        return $websiteIds;
    }

    public function filter($str)
    {
        $str  = $this->formatCustomVariables($str);
        $html = $this->_filterProvider->getPageFilter()->filter($str);

        return $html;
    }

    /**
     * Check if Configurable Product is Available or Not to Complete Preorder.
     *
     * @param int $productId
     * @param int $qty
     * @param int $parentId
     *
     * @return bool
     */
    public function isConfigAvailable($productId, $qty, $parentId)
    {
        if ($this->isAvailable($productId, $qty)) {
            if ($this->isAvailable($parentId, $qty, 1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get config value
     */
    public function getConfigValue($value = '')
    {
        return $this->scopeConfig
            ->getValue(
                $value,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Get base url
     */
    public function getBaseUrl()
    {
        return $this->_storeManager
            ->getStore()
            ->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
    }

    /**
     * Get current url
     */
    public function getCurrentUrls()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    public function getIp()
    {
        //Just get the headers if we can or else use the SERVER global
        if (function_exists('apache_request_headers')) {

            $headers = apache_request_headers();

        } else {

            $headers = $_SERVER;

        }

        //Get the forwarded IP if it exists
        if (array_key_exists('X-Forwarded-For', $headers) && filter_var($headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {

            $the_ip = $headers['X-Forwarded-For'];

        } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers) && filter_var($headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
        ) {

            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];

        } else {

            $the_ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

        }

        return $the_ip;
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
        $store     = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofpreorder/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $result;
    }

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

    public function getSymbol()
    {
        $currency = $this->_localeCurrency->getCurrency($this->_storeManager->getStore()->getCurrentCurrencyCode());
        $symbol   = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();

        if (! $symbol) {
            $symbol = '';
        }

        return $symbol;
    }

    public function getMediaUrl()
    {
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                                              ->getStore()
                                              ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $storeMediaUrl;
    }

    public function getFieldPrefix()
    {
        return 'loffield_';
    }

    public function getCurrentProduct()
    {
        if ($this->coreRegistry->registry('product')) {
            return $this->coreRegistry->registry('product');
        }

        return false;
    }

    public function getCurrentCategory()
    {
        if ($this->coreRegistry->registry('current_category')) {
            return $this->coreRegistry->registry('current_category');
        }

        return false;
    }

    /**
     * Quote object getter
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        $quote = $this->checkoutSession->getQuote();

        return $quote;
    }

    public function getCustomer($customerId = '')
    {
        $customer = $this->customerSession->getCustomer();

        return $customer;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function formatCustomVariables($str)
    {
        $customer = $this->getCustomer();
        $quote    = $this->getQuote();
        $category = $this->getCurrentCategory();
        $store    = $this->_storeManager->getStore();
        $product  = $this->getCurrentProduct();

        $data   = [
            "customer" => $customer,
            "quote"    => $quote,
            "product"  => $product,
            "category" => $category,
            "store"    => $store,
        ];
        $result = $this->filterManager->template($str, ['variables' => $data]);

        return $result;
    }

    public function getProductById($productId)
    {
        if (! isset($this->_product_item)) {
            $this->_product_item = [];
        }
        if (! isset($this->_product_item[$productId]) || (isset($this->_product_item[$productId]) && ! $this->_product_item[$productId])) {
            $collection = $this->_productCollection->create();
            $collection->addFieldToFilter('entity_id', $productId);
            $collection->addAttributeToSelect('*');
            if ($collection->count()) {
                $this->_product_item[$productId] = $collection->getFirstItem();
            } else {
                $this->_product_item[$productId] = false;
            }
        }

        return $this->_product_item[$productId];
    }

    /**
     * Check Product is Preorder or Not.
     *
     * @param int   $productId
     * @param mixed $product
     * @return bool
     */
    public function isPreorder($productId, $product = null)
    {
        if (! $this->getIsEnabled()) {
            return false;
        }

        $productId = (int) $productId;

        if (! $this->isValidProductId($productId)) {
            return false;
        }

        if (! $product) {
            $product = $this->getProduct($productId);
        }

        if (! $product) {
            return false;
        }

        //Check auto setting before manual
        if ($this->getCheckAutoBeforeManual()) {
            $product_type = $product->getTypeId();
            if ($this->checkAvailableProductType($product_type)) {
                $is_preorder_number = $this->checkAutoPreorder($productId);
                if ($is_preorder_number !== 0) {
                    return $is_preorder_number;
                }
            }
        }

        //Case 1: Check manual Preorder from product attribute
        $flag = $this->isPreorderAvailability($product);

        if ($flag) {
            $preorderStatus = $product->getLofPreorder();

            return (int) $preorderStatus;
        } else {
            /** removed because performance - panda
             * $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
             * $parent_product = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($productId);
             * if(isset($parent_product[0]) && $parent_product[0] != $productId){
             * $productParent = $this->getProduct($parent_product[0]);
             * $flag = $this->isPreorderAvailability($productParent);
             * if($flag){
             * $preorderStatus = $productParent->getLofPreorder();
             * return (int)$preorderStatus;
             * }
             * }*/
        }

        //Check auto setting after manual
        if (! $this->getCheckAutoBeforeManual()) {
            $product_type = $product->getTypeId();
            if ($this->checkAvailableProductType($product_type)) {
                $is_preorder_number = $this->checkAutoPreorder($productId);
                if ($is_preorder_number !== 0) {
                    return $is_preorder_number;
                }
            }
        }

        return false;
    }

    public function checkAvailableProductType($productTypeId = "simple")
    {
        $notAvailableTypes = [
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
            \Magento\Bundle\Model\Product\Type::TYPE_CODE,
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
        ];
        if (! in_array($productTypeId, $notAvailableTypes)) {
            return true;
        }

        return false;
    }

    public function getMsgWarningQtyInCart($productId, $product_name = '', $qty = 1)
    {
        $stock = $this->getStockStatusData($productId);
        if ($stock && isset($stock['qty']) && ((int) $stock['qty'] > 0 && (int) $qty > (int) $stock['qty'])) {
            $msg         = $this->getMsgWarningInCartConfig();
            $preorderQty = (int) $qty - (int) $stock['qty'];
            $msg         = str_replace(['%1$s', '%2$s'], [$product_name, $preorderQty], $msg);
            $msg         = str_replace("\n", '<br>', $msg);

            return $msg;
        }

        return "";
    }

    /**
     * Get Html Block of Pay Preorder Amount (Partial Preorder).
     *
     * @param int $productId
     * @return bool|int
     */
    public function checkAutoPreorder($productId)
    {
        $stock = $this->getStockStatusData($productId);
        //Case 2: Check qty > 0 and setting enable will return false
        if ($this->getDisablePreorderQtyAboveZero()) {
            if ($stock && isset($stock['qty']) && (0 < (int) $stock['qty'])) {
                return false;
            }
        }
        //Case 3: Check qty <= 0 and setting enable will return true
        if ($this->getAutoPreorderQtyBellowZero()) {
            if ($stock && isset($stock['qty']) && (0 >= (int) $stock['qty'])) {
                return true;
            }
        }

        return 0;
    }

    /**
     * Get Html Block of Pay Preorder Amount (Partial Preorder).
     *
     * @return string
     */
    public function getPayPreOrderHtml()
    {
        $html    = '';
        $type    = $this->getPreorderType();
        $percent = $this->getPreorderPercent();
        if ($type == 1 && $percent > 0) {
            $html .= "<div class='lof-msg-box lof-info lof-pay-preorer-amount'>";
            $html .= __('Pay %1% as Preorder.', $percent);
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get Preorder Percent.
     *
     * @param int|null    $productId
     * @param Object|null $product
     * @return int
     */
    public function getPreorderPercent($productId = 0, $product = null)
    {
        $config = (float) $this->getConfig('settings/preorder_percent');

        return $config;
    }

    /**
     * Get Preorder Percent.
     *
     * @return int
     */
    public function isEnableCheckAvailbility()
    {
        $config = $this->getConfig('settings/enable_check_availbility');

        return $config;
    }

    /**
     * @param null $product
     * @return bool
     */
    public function isPreorderAvailability($product = null)
    {
        $is_check_availbility = $this->isEnableCheckAvailbility();
        if (! $is_check_availbility) {
            return true;
        }

        if ($product) {
            $availability = $product->getPreorderAvailability();
            if ($availability) {
                $today = (new \DateTime())->format('Y-m-d');
                if ($availability > $today) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param        $productId
     * @param null   $product
     * @param string $css_class
     * @return string
     */
    public function getPreOrderInfoBlock($productId, $product = null, $css_class = 'lof-msg-box lof-info')
    {
        $html     = '';
        $flag     = false;
        $dispDate = '';
        if (! $product) {
            $product = $this->getProduct($productId);
        }
        $msg = $this->getPreorderMsg($product);
        $msg = str_replace("\n", '<br>', $msg);
        if ($msg != '') {
            $html .= "<div class='" . $css_class . "'>";
            $html .= $msg;
            $html .= '</div>';
        }

        if ($this->getShowAvailbilityDate()) {
            $availability = $product->getPreorderAvailability();
            if ($availability) {
                $today    = (new \DateTime())->format('Y-m-d');
                $date     = date_create($availability);
                $dispDate = date_format($date, 'l jS F Y');

                if ($availability > $today) {
                    $flag = true;
                }
            }

            if ($flag) {
                $html .= "<div class='" . $css_class . " lof-availability-block'>";
                $html .= "<span class='lof-date-title'>";
                $html .= __('Available On: ');
                $html .= '</span>';
                $html .= "<span class='lof-date'>" . $dispDate . '</span>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    /**
     * Get Product by Id.
     *
     * @param int $productId
     *
     * @return object
     */
    public function getProduct($productId)
    {
        return $this->_product->create()->load($productId);
    }

    /**
     * Check Whether Product Id is Valid or Not
     *
     * @param int $productId
     *
     * @return bool
     */
    public function isValidProductId($productId)
    {
        $preorderCompleteProductId = $this->getPreorderCompleteProductId();
        if ($productId == $preorderCompleteProductId) {
            return false;
        }
        if ($productId == '' || $productId == 0) {
            return false;
        }

        return true;
    }

    /**
     * Get Preorder Complete Product Id.
     *
     * @return int
     */
    public function getPreorderCompleteProductId()
    {
        $productModel = $this->_product->create();
        $productId    = (int) $productModel->getIdBySku('preorder_complete');

        return $productId;
    }

    public function getStockStatusData($productId)
    {
        if (! isset($this->_stock_status_data)) {
            $this->_stock_status_data = [];
        }
        if (! isset($this->_stock_status_data[$productId])) {
            $this->_stock_status_data[$productId] = $this->getStockDetails($productId);
        }

        return $this->_stock_status_data[$productId];
    }

    /**
     * Get Stock Status of Product.
     *
     * @param int $productId
     *
     * @return bool
     */
    public function getStockStatus($productId)
    {
        $stockDetails = $this->getStockStatusData($productId);

        return $stockDetails['is_in_stock'];
    }

    /**
     * Get Stock Details of Product.
     *
     * @param int $productId
     *
     * @return array
     */
    public function getStockDetails($productId)
    {
        $connection   = $this->_resource->getConnection();
        $stockDetails = ['is_in_stock' => 0, 'qty' => 0];
        $collection   = $this->_productCollection->create()->addAttributeToSelect('name');
        $table        = $connection->getTableName('cataloginventory_stock_item');
        $bind         = 'product_id = entity_id';
        $cond         = '{{table}}.stock_id = 1';
        $type         = 'left';
        $alias        = 'is_in_stock';
        $field        = 'is_in_stock';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $alias = 'qty';
        $field = 'qty';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $collection->addFieldToFilter('entity_id', $productId);
        foreach ($collection as $value) {
            $stockDetails['qty']         = $value->getQty();
            $stockDetails['is_in_stock'] = $value->getIsInStock();
            $stockDetails['name']        = $value->getName();
        }

        return $stockDetails;
    }

    /**
     * Get Preorder Type.
     *
     * @return int
     */
    public function getPreorderType()
    {
        $config = 'settings/preorder_type';

        return $this->getConfig($config);
    }

    /**
     * Get Url To Check Configurable Product is Preorder or Not.
     *
     * @return string
     */
    public function getCheckConfigUrl()
    {
        return $this->_urlBuilder->getUrl('lofpreorder/preorder/check/');
    }

    /**
     * Check Product is Child Product or Not.
     *
     * @return bool
     */
    public function isChildProduct()
    {
        $productId        = $this->_request->getParam('id');
        $productModel     = $this->_product->create();
        $product          = $productModel->load($productId);
        $productType      = $product->getTypeID();
        $productTypeArray = ['bundle', 'grouped'];
        if (in_array($productType, $productTypeArray)) {
            return true;
        }

        return false;
    }

    public function getAssociatedId($attribute, $product)
    {
        $configModel = $this->_configurable;
        $product     = $configModel->getProductByAttributes($attribute, $product);
        $productId   = $product->getId();

        return $productId;
    }

    /**
     * Create Preorder Complete Product if Not Exists.
     */
    public function createPreOrderProduct()
    {
        $preorderProductId = $this->getPreorderCompleteProductId();
        $attributeSetId    = $this->_catalogProduct->getDefaultAttributeSetId();

        if ($preorderProductId == 0 || $preorderProductId == '') {
            try {
                $websiteIds = $this->getWebsiteIds();
                $stockData  = [
                    'use_config_manage_stock' => 0,
                    'manage_stock'            => 0,
                    'is_in_stock'             => 1,
                    'qty'                     => 999999999,
                ];

                $preorderProduct = $this->_product->create();
                $preorderProduct->setSku('preorder_complete');
                $preorderProduct->setName('Complete PreOrder');
                $preorderProduct->setAttributeSetId($attributeSetId);
                $preorderProduct->setCategoryIds([2]);
                $preorderProduct->setWebsiteIds($websiteIds);
                $preorderProduct->setStatus(1);
                $preorderProduct->setVisibility(1);
                $preorderProduct->setTaxClassId(0);
                $preorderProduct->setTypeId('virtual');
                $preorderProduct->setPrice(0);
                $preorderProduct->setStockData($stockData);
                $preorderProduct->save();
                $this->addImage($preorderProduct);
                $this->setCustomOption($preorderProduct);
                $this->updateProduct($preorderProduct->getId());
            } catch (\Exception $e) {
                $e->getMessage();
            }
        }
    }

    /**
     * Add Image to Preorder Complete Product
     *
     * @param object $product
     */
    public function addImage($product)
    {
        $path = $this->getMediaPath() . 'preorder/images/preorder.png';
        if (file_exists($path)) {
            $types = ['image', 'small_image', 'thumbnail'];
            $product->addImageToMediaGallery($path, $types, false, false);
            $product->save();
        }
    }

    /**
     * Get Mediad Path.
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_filesystem
            ->getDirectoryRead(DirectoryList::MEDIA)
            ->getAbsolutePath();
    }

    /**
     * Check Configurable Product is Preorder or Not.
     *
     * @param int $productId
     *
     * @return bool
     */
    public function isConfigPreorder($productId, $stockStatus = '')
    {
        $isProduct  = false;
        $collection = $this->_productCollection->create();
        $collection->addFieldToFilter('entity_id', $productId);
        $collection->addAttributeToSelect('*');
        foreach ($collection as $item) {
            $product   = $item;
            $isProduct = true;
            break;
        }
        if ($isProduct) {
            $productType = $product->getTypeId();
            if ($productType == 'configurable') {
                $configModel    = $this->_configurable;
                $usedProductIds = $configModel->getUsedProductIds($product);
                foreach ($usedProductIds as $usedProductId) {
                    if ($stockStatus != '') {
                        if ($this->isPreorder($usedProductId, $stockStatus)) {
                            return true;
                        }
                    } else {
                        if ($this->isPreorder($usedProductId)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check Product is Partial Preorder or Not.
     *
     * @param int $productId
     *
     * @return bool
     */
    public function isPartialPreorder($productId)
    {
        if (! $this->isPreorder($productId)) {
            return false;
        }

        $preorderType = $this->getPreorderType();

        if ($preorderType == 1) {
            return true;
        }

        return false;
    }

    /**
     * @param $product
     * @param $productId
     * @return float|int
     */
    public function getPreorderPrice($product, $productId)
    {
        $price = $this->getPrice($product);
        if ($this->isPartialPreorder($productId)) {
            $preorderPercent = $this->getPreorderPercent();
            if ($preorderPercent > 0) {
                $price = ($price * $preorderPercent) / 100;
            } else {
                $price = 0.0;
            }
        }

        return $price;
    }

    public function writeLog($message_type, $message)
    {
        $this->logger->critical($message_type, ['exception' => $message]);

        return $this;
    }

    /**
     * Get Product's Price.
     *
     * @param mixed $product
     * @return float
     */
    public function getPrice($product)
    {
        $price = $product->getFinalPrice();

        return $price;
    }

    /**
     * Get First Object From Collection
     *
     * @param array | int | string $values
     * @param array | string       $fields
     * @param object               $collection
     *
     * @return $object
     */
    public function getDataByField($values, $fields, $collection)
    {
        $item = false;

        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $field      = $fields[$key];
                $collection = $collection->addFieldToFilter($field, $value);
            }
        } else {
            $collection = $collection->addFieldToFilter($fields, $values);
        }
        foreach ($collection as $item) {
            return $item;
        }

        return $item;
    }

    /**
     * Get Order by Id.
     *
     * @param int $orderId
     *
     * @return object
     */
    public function getOrder($orderId)
    {
        return $this->_order->create()->load($orderId);
    }

    /**
     * Get Preorder Complete Product's Options.
     *
     * @return json
     */
    public function getPreorderCompleteOptions($json = true)
    {
        $array     = [];
        $productId = (int) $this->getPreorderCompleteProductId();
        $product   = $this->_product->create()->load($productId);
        foreach ($product->getOptions() as $option) {
            $optionId    = $option->getId();
            $optionTitle = $option->getTitle();
            $array[]     = ['id' => $optionId, 'title' => $optionTitle];
        }
        if ($json) {
            return json_encode($array);
        }

        return $array;
    }

    public function getPreorderOrderedItem($itemId)
    {
        $collection = $this->_preorderCollection->create();
        $item       = $this->getDataByField($itemId, 'item_id', $collection);

        return $item;
    }

    /**
     * Get Preorder Status.
     *
     * @param mixed $item
     * @param int   $itemId
     * @return int
     */
    public function getPreorderStatus($itemId = 0, $item = null)
    {
        $status = 0;
        if (! $item && $itemId) {
            $collection = $this->_preorderCollection->create();
            $item       = $this->getDataByField($itemId, 'item_id', $collection);
        }
        if ($item) {
            $status = $item->getStatus() + 1;
        }

        return $status;
    }

    /**
     * Check Product is Available or Not to Complete Preorder.
     *
     * @param int $productId
     * @param int $qty
     * @param int $isQty
     *
     * @return bool
     */
    public function isAvailable($productId, $qty, $isQty = 0)
    {
        $stockDetails = $this->getStockDetails($productId);
        if ($stockDetails['is_in_stock'] == 1) {
            if ($isQty == 0) {
                if ($stockDetails['qty'] > $qty) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Email Id's of Customers who Ordered Preorder Items.
     *
     * @param int $productId
     *
     * @return array
     */
    public function getCustomerEmailIds($productId)
    {
        $emailIds   = [];
        $collection = $this->_preorderCollection
            ->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToSelect('customer_email');
        foreach ($collection as $value) {
            $emailIds[] = $value->getCustomerEmail();
        }
        $emailIds = array_unique($emailIds);

        return $emailIds;
    }

    /**
     * Get Account Login Url For Customer.
     *
     * @return string
     */
    public function getLogInUrl()
    {
        return $this->_urlBuilder->getUrl('customer/account/login/');
    }

    /**
     * Send Notification Email when Product is in Stock.
     *
     * @param array emailIds
     * @param string $productName
     */
    public function sendNotifyEmail($emailIds, $productName)
    {
        $adminEmail = $this->getAdminEmail();
        $loginUrl   = $this->getLogInUrl();
        if ($adminEmail != '') {
            $area            = Area::AREA_FRONTEND;
            $store           = $this->_storeManager->getStore()->getId();
            $msg             = __('Product "%1" is in stock. Please go your account to complete preorder.', $productName);
            $templateOptions = ['area' => $area, 'store' => $store];
            $templateVars    = [
                'store'     => $this->_storeManager->getStore(),
                'message'   => $msg,
                'login_url' => $loginUrl,
            ];
            $from            = ['email' => $adminEmail, 'name' => 'Store Owner'];
            $in_stock_notify = $this->getIsStockNotifyTemplate();
            foreach ($emailIds as $emailId) {
                $templateVars['customer_name'] = '';
                $this->_inlineTranslation->suspend();
                //$to = [$emailId];//work for magento 2.3.3 or older
                $to        = $emailId;
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($in_stock_notify)
                    ->setTemplateOptions($templateOptions)
                    ->setTemplateVars($templateVars)
                    ->setFrom($from)
                    ->addTo($to)
                    ->getTransport();
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            }
        }
    }

    /**
     * Get Admin Email Id.
     *
     * @return string
     */
    public function getShowAvailbilityDate()
    {
        return $this->getConfig('display_option/show_availbility_date');
    }

    public function getPreorderWarningInOrder()
    {
        return $this->getConfig('display_option/preorder_warning_in_order');
    }

    /**
     * Get Admin Email Id.
     *
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->getConfig('settings/preorder_email');
    }

    /**
     * Get Auto Email Configuration.
     *
     * @return bool
     */
    public function getAutoEmail()
    {
        return $this->getConfig('settings/preorder_email_type');
    }

    /**
     * Get Auto Email Configuration.
     *
     * @return bool
     */
    public function getAddtocartButtonText()
    {
        return $this->getConfig('display_option/addtocart_button_text');
    }

    /**
     * Get Auto Email Configuration.
     *
     * @return bool
     */
    public function getMsgWarningQtyBellowZero()
    {
        return $this->getConfig('display_option/preorder_warning_bellow_zero');
    }

    /**
     * Get Auto Email Configuration.
     *
     * @return bool
     */
    public function getShowPreorderMsgOnCategory()
    {
        return $this->getConfig('display_option/show_preorder_on_category');
    }

    public function getMsgWarningInCartConfig()
    {
        return $this->getConfig('display_option/preorder_warning_in_cart');
    }

    /**
     * Get Auto Preorder Configuration.
     *
     * @return bool
     */
    public function getCheckAutoBeforeManual()
    {
        return $this->getConfig('settings/preorder_auto_before_manual');
    }

    /**
     * Get Auto Preorder Configuration.
     *
     * @return bool
     */
    public function getIsStockNotifyTemplate()
    {
        return $this->getConfig('settings/in_stock_notify_template');
    }

    /**
     * Get Auto Preorder with Qty Bellow Zero Configuration.
     *
     * @return bool
     */
    public function getAutoPreorderQtyBellowZero()
    {
        return $this->getConfig('settings/preorder_auto_qty_bellow_zero');
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @return bool
     */
    public function getDisablePreorderQtyAboveZero()
    {
        return $this->getConfig('settings/preorder_disable_qty_above_zero');
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @param string $msg
     * @param Magento/Catalog/Model/Product|null $product
     * @return string
     */
    public function convertMsg($msg, $product = null)
    {
        if ($product) {
            $product_data = $product->getData();
            if ($product_data) {
                foreach ($product_data as $key => $val) {
                    if ($key && $val && is_string($key) && is_string($val)) {
                        $replace_key = "{" . $key . "}";
                        $msg         = str_replace($replace_key, $val, $msg);
                    }
                }
            }
        }

        return $msg;
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @param Magento/Catalog/Model/Product|null $product
     * @return string
     */
    public function getPreorderMsg($product = null)
    {
        $msg = $this->getPreorderMsgConfig();
        if ($product) {
            $custom_msg = $product->getPreorderMsg();
            $custom_msg = $custom_msg ? trim($custom_msg) : null;
            if ($custom_msg) {
                $msg = $custom_msg;
            }
        }
        $msg = $this->convertMsg($msg, $product);

        return $msg;
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @return bool
     */
    public function getPreorderMsgConfig()
    {
        return $this->getConfig('display_option/preorder_msg');
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @return bool
     */
    public function getProductPreorderText()
    {
        return $this->getConfig('display_option/product_preorder_text');
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @return bool
     */
    public function getProductPreorderTextPosition()
    {
        return $this->getConfig('display_option/product_preorder_text_position');
    }

    /**
     * Get Auto Disable Preorder with Qty Above Zero Configuration.
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->getConfig('settings/enabled');
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

    public function getOrderData($order_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order         = $objectManager->get('Magento\Sales\Model\Order')->load($order_id);

        return $order;
    }

    /**
     * Get Customer Id's who Ordered Preorder Items.
     *
     * @param int $productId
     *
     * @return array
     */
    public function getCustomerIds($productId)
    {
        $emailIds   = [];
        $collection = $this->_preorderCollection
            ->create()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToSelect('customer_id');
        foreach ($collection as $value) {
            $emailIds[] = $value->getCustomerId();
        }
        $emailIds = array_unique($emailIds);

        return $emailIds;
    }
}
