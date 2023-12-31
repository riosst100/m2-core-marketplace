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

class Preorder extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;


    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;
        /**
         * @var \Magento\Catalog\Model\ProductRepositoryFactory
         */
    protected $_product;
        /**
         * @var Products
         */
    protected $_productCollection;

    public $_resource;

    /**
     * @var Items
     */
    protected $_itemCollection;
    /**
     * @var CollectionFactory
     */
    protected $_preorderCollection;



    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $coreRegistry,
        Products $productCollection,
        Items $itemCollection,
        CollectionFactory $preorderCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductRepositoryFactory $productRepository
    ) {
        parent::__construct($context);
        $this->_storeManager   = $storeManager;
        $this->_localeDate     = $localeDate;
        $this->_objectManager  = $objectManager;
        $this->coreRegistry    = $coreRegistry;
        $this->_productCollection = $productCollection;
        $this->_resource = $resource;
        $this->_product = $productRepository;
        $this->_preorderCollection = $preorderCollection;
        $this->_itemCollection = $itemCollection;
    }


    public function getProductFactory()
    {
        return $this->_product;
    }
    public function getProductBySku($sku)
    {
        $product = $this->_product->create();
        return $product->get($sku);
    }
      /**
       * Get All Website Ids.
       *
       * @return array
       */
    public function getWebsiteIds()
    {
        $websiteIds = [];
        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            $websiteIds[] = $website->getId();
        }
        return $websiteIds;
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
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofpreorder/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
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
      * Get store identifier
      *
      * @return  int
      */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getProductById($productId)
    {
        if (!isset($this->_product_item)) {
            $this->_product_item = [];
        }
        if (!isset($this->_product_item[$productId]) || (isset($this->_product_item[$productId]) && !$this->_product_item[$productId])) {
            $collection = $this->_productCollection->create();
            $collection->addFieldToFilter('entity_id', $productId);
            $collection->addAttributeToSelect('*');
            if ($collection->count()) {
                $this->_product_item[$productId]=$collection->getFirstItem();
            } else {
                $this->_product_item[$productId] = false;
            }
        }
        return $this->_product_item[$productId];
    }
    /**
     * Check Product is Preorder or Not.
     *
     * @param int  $productId
     * @param mixed $product
     * @return bool
     */
    public function isPreorder($productId, $product = null)
    {
        if (!$this->getIsEnabled()) {
            return false;
        }
        $productId = (int) $productId;

        if (!$this->isValidProductId($productId)) {
            return false;
        }
        if (!$product) {
            $product = $this->getProduct($productId);
        }
        if (!$product) {
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
            return (int)$preorderStatus;
        } else {
            /** removed because performance - panda
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $parent_product = $objectManager->create('Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable')->getParentIdsByChild($productId);
            if(isset($parent_product[0]) && $parent_product[0] != $productId){
                $productParent = $this->getProduct($parent_product[0]);
                $flag = $this->isPreorderAvailability($productParent);
                if($flag){
                    $preorderStatus = $productParent->getLofPreorder();
                    return (int)$preorderStatus;
                }
            }*/
        }
        //Check auto setting after manual
        if (!$this->getCheckAutoBeforeManual()) {
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
        $notAvailableTypes = [\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE,
                            \Magento\Bundle\Model\Product\Type::TYPE_CODE,
                            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE
                        ];
        if (!in_array($productTypeId, $notAvailableTypes)) {
            return true;
        }
        return false;
    }

     /**
      * Get Html Block of Pay Preorder Amount (Partial Preorder).
      * @param int $productId
      * @return bool|int
      */
    public function checkAutoPreorder($productId)
    {
        $stock = $this->getStockStatusData($productId);
        //Case 2: Check qty > 0 and setting enable will return false
        if ($this->getDisablePreorderQtyAboveZero()) {
            if ($stock && isset($stock['qty']) && (0 < (int)$stock['qty'])) {
                return false;
            }
        }
        //Case 3: Check qty <= 0 and setting enable will return true
        if ($this->getAutoPreorderQtyBellowZero()) {
            if ($stock && isset($stock['qty']) && (0 >= (int)$stock['qty'])) {
                return true;
            }
        }
        return 0;
    }

    /**
     * Get Preorder Percent.
     * @return int
     */
    public function isEnableCheckAvailbility()
    {
        $config = $this->getConfig('settings/enable_check_availbility');
        return $config;
    }

    public function isPreorderAvailability($product = null)
    {
        $is_check_availbility = $this->isEnableCheckAvailbility();
        if (!$is_check_availbility) {
            return true;
        }
        if ($product) {
            $availability = $product->getPreorderAvailability();
            if ($availability != '') {
                $today = date('m/d/y');
                $date = date_create($availability);
                $date = date_format($date, 'm/d/y');
                if ($date > $today) {
                    return true;
                }
            }
        }
        return false;
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
        return $this->_product->create()->getById($productId);
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
     * Get Prorder Complete Product Id.
     *
     * @return int
     */
    public function getPreorderCompleteProductId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productModel = $objectManager->create('Magento\Catalog\Model\Product');
        $productId = (int) $productModel->getIdBySku('preorder_complete');

        return $productId;
    }

    public function getStockStatusData($productId)
    {
        if (!isset($this->_stock_status_data)) {
            $this->_stock_status_data = [];
        }
        if (!isset($this->_stock_status_data[$productId])) {
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
        $connection = $this->_resource->getConnection();
        $stockDetails = ['is_in_stock' => 0, 'qty' => 0];
        $collection = $this->_productCollection
                            ->create()
                            ->addAttributeToSelect('name');
        $table = $connection->getTableName('cataloginventory_stock_item');
        $bind = 'product_id = entity_id';
        $cond = '{{table}}.stock_id = 1';
        $type = 'left';
        $alias = 'is_in_stock';
        $field = 'is_in_stock';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $alias = 'qty';
        $field = 'qty';
        $collection->joinField($alias, $table, $field, $bind, $cond, $type);
        $collection->addFieldToFilter('entity_id', $productId);
        foreach ($collection as $value) {
            $stockDetails['qty'] = $value->getQty();
            $stockDetails['is_in_stock'] = $value->getIsInStock();
            $stockDetails['name'] = $value->getName();
        }

        return $stockDetails;
    }

    public function getProductModel()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product');
        return $product;
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
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->getConfig('settings/enabled');
    }
}
