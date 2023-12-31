<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Block\Widget;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Rule\Model\Condition\Sql\Builder as SqlBuilder;
use Magento\Widget\Helper\Conditions;

class SuggestionProduct extends \Magento\CatalogWidget\Block\Product\ProductsList {

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory
     */
    protected $sellerProductCollectionFactory;

    /**
     * @var \Lofmp\FavoriteSeller\Helper\ConfigData
     */
    protected $moduleConfigData;

    /**
     * @var \Lofmp\FavoriteSeller\Model\Config\Source\SuggestionCriteria
     */
    protected $suggestionCriteria;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
     * @param  \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory $sellerProductCollectionFactory,
     * @param  \Magento\Customer\Model\SessionFactory $customerSession,
     * @param   \Lofmp\FavoriteSeller\Helper\ConfigData $moduleConfigData,
     * @param   \Lofmp\FavoriteSeller\Model\Config\Source\SuggestionCriteria $suggestionCriteria,
     * @param   Context $context,
     * @param   CollectionFactory $productCollectionFactory,
     * @param   Visibility $catalogProductVisibility,
     * @param   HttpContext $httpContext,
     * @param   SqlBuilder $sqlBuilder, Rule $rule,
     * @param   Conditions $conditionsHelper,
     * @param   array $data = [],
     * @param   Json $json = null,
     * @param   LayoutFactory $layoutFactory = null,
     * @param   EncoderInterface $urlEncoder = null,
     * @param   CategoryRepositoryInterface $categoryRepository = null,
     * @param   \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param   \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory $sellerProductCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Lofmp\FavoriteSeller\Helper\ConfigData $moduleConfigData,
        \Lofmp\FavoriteSeller\Model\Config\Source\SuggestionCriteria $suggestionCriteria,
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        HttpContext $httpContext,
        SqlBuilder $sqlBuilder, Rule $rule,
        Conditions $conditionsHelper,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null,
        CategoryRepositoryInterface $categoryRepository = null
    )
    {
        $this->customerSession = $customerSession;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->sellerProductCollectionFactory = $sellerProductCollectionFactory;
        $this->moduleConfigData = $moduleConfigData;
        $this->suggestionCriteria = $suggestionCriteria;
        $this->reviewFactory = $reviewFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->_localeDate               = $localeDate;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder,
            $categoryRepository
        );
    }

    public function getCustomer(){
        return $this->customerSession->getCustomer(); //Get Current Customer Data
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function createCollection()
    {
        $customerId = $this->customerSession->create()->getCustomerId();
        $subscriptionCollection = $this->subscriptionCollectionFactory->create();
        $subscription = $subscriptionCollection
            ->addFieldToFilter('customer_id',  ['eq' => $customerId])
            ->addFieldToSelect('seller_id')
            ->setOrder("creation_time", 'DESC')
            ->setPageSize(100)
            ->load();
        $sellerIds = [];
        foreach ($subscription as $item){
            $sellerIds[] = $item->getSellerId();
        }

        $orderBy = $this->moduleConfigData->getConfigValue('favoriteseller_config/product_view/suggest_by');
        $orderBy = $this->suggestionCriteria->optionToCondition($orderBy);

        $numberProduct = $this->getData('number_product');
        $suggestionCollection = null;
        switch ($orderBy){
            case $this->suggestionCriteria::TOP_RATED :
                $suggestionCollection = $this->getTopRatedProducts($sellerIds, $numberProduct);
                break;
            case $this->suggestionCriteria::FEATURED:
                $suggestionCollection = $this->getFeaturedProducts($sellerIds, $numberProduct);
                break;
            case $this->suggestionCriteria::NEW_ARRIVAL:
                $suggestionCollection = $this->getNewArrivalProducts($sellerIds, $numberProduct);
                break;
            case $this->suggestionCriteria::RANDOM:
                $suggestionCollection = $this->getRandomProducts($sellerIds, $numberProduct);
                break;
            case $this->suggestionCriteria::DEALS:
                $suggestionCollection = $this->getDealsProducts($sellerIds, $numberProduct);
                break;
            case $this->suggestionCriteria::CREATED_AT:
            default:
                $suggestionCollection = $this->getLastedProducts($sellerIds, $numberProduct);
                break;
        }

        if($suggestionCollection){
            $suggestionCollection->load();
        }
        return $suggestionCollection;
    }
    /**
     * Get Lasted Products
     * @param int[]|null $sellerIds
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getLastedProducts($sellerIds, $limit){
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->setOrder('created_at', 'DESC')
            ->setPageSize($limit)
            ->setCurPage(1);

        $collection->getSelect()->join(
            ['seller_product' => $sellerProductTable],
            'e.entity_id = seller_product.product_id',
            ['seller_id']
        );
        $collection->getSelect()->group("e.entity_id");
        return $collection;
    }
    /**
     * Get Top Rated Products
     * @param int[]|null $sellerIds
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getTopRatedProducts($sellerIds, $limit){
        $storeId = $this->_storeManager->getStore(true)->getId();
        $_resource = $this->reviewFactory->create()->getResource();
        $sellerProductTable = $_resource->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('status', ['eq' => 1])
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->setPageSize($limit)
            ->setCurPage(1)
            ->joinField(
                'lof_review',
                $_resource->getTable('review_entity_summary'),
                'reviews_count',
                'entity_pk_value=entity_id',
                'at_lof_review.store_id=' . (int)$storeId,
                'lof_review > 0',
                'left'
            );

        $collection->getSelect()->join(
            ['seller_product' => $sellerProductTable],
            'e.entity_id = seller_product.product_id',
            ['seller_id']
        );

        $collection->getSelect()->group("e.entity_id");
        $collection->getSelect()->order('lof_review DESC');
        return $collection;
    }

    /**
     * Get Featured Products
     * @param int[]|null $sellerIds
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getFeaturedProducts($sellerIds, $limit){
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(array(array( 'attribute'=>'featured', 'eq' => '1')))
            ->addStoreFilter()
            ->setOrder('created_at', 'DESC')
            ->setPageSize($limit)
            ->setCurPage(1);

        $collection->getSelect()->join(
            ['seller_product' => $sellerProductTable],
            'e.entity_id = seller_product.product_id',
            ['seller_id']
        );
        $collection->getSelect()->group("e.entity_id");
        return $collection;
    }

    /**
     * Get New Arrival Products
     * @param int[]|null $sellerIds
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getNewArrivalProducts($sellerIds, $limit){
        $todayStartOfDayDate = $this->_localeDate->date()
            ->setTime(0, 0)
            ->format('Y-m-d H:i:s');

        $todayEndOfDayDate = $this->_localeDate->date()
            ->setTime(23, 59, 59)
            ->format('Y-m-d H:i:s');

        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter(
                'news_from_date',
                [
                'or' => [
                0 => ['date' => true, 'to' => $todayEndOfDayDate],
                1 => ['is' => new \Zend_Db_Expr('null')],
                ]
                ],
                'left'
                )->addAttributeToFilter(
                'news_to_date',
                [
                'or' => [
                0 => ['date' => true, 'from' => $todayStartOfDayDate],
                1 => ['is' => new \Zend_Db_Expr('null')],
                ]
                ],
                'left'
                )->addAttributeToFilter(
                [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
                ]
                )->addAttributeToSort(
                'news_from_date',
                'desc'
                );

        $collection->setOrder('created_at', 'DESC')
                ->setPageSize($limit)
                ->setCurPage(1);

        $collection->getSelect()->join(
            ['seller_product' => $sellerProductTable],
            'e.entity_id = seller_product.product_id',
            ['seller_id']
        );
        $collection->getSelect()->group("e.entity_id");
        return $collection;
    }

    /**
     * Get Random Products
     * @param int[]|null $sellerIds
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getRandomProducts($sellerIds, $limit){
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->setPageSize($limit)
            ->setCurPage(1);

        $collection->getSelect()->join(
            ['seller_product' => $sellerProductTable],
            'e.entity_id = seller_product.product_id',
            ['seller_id']
        );
        $collection->getSelect()->group("e.entity_id");
        $collection->getSelect()->order('rand()');
        return $collection;
    }

    /**
     * Get Deals Products
     * @param int[]|null $sellerIds
     * @param int $limit
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getDealsProducts($sellerIds, $limit){
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds())
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addStoreFilter();

        $collection->getSelect()->join(
            ['seller_product' => $sellerProductTable],
            'e.entity_id = seller_product.product_id',
            ['seller_id']
        );

        $collection->addAttributeToFilter(
            'special_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter(
            'special_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ]
            ],
            'left'
        )->addAttributeToFilter([
            ['attribute' => 'special_from_date', 'is' => new \Zend_Db_Expr('not null')],
            ['attribute' => 'special_to_date', 'is' => new \Zend_Db_Expr('not null')],
        ])
        ->addMinimalPrice()
        ->addUrlRewrite()
        ->addTaxPercents()
        ->addFinalPrice();

        $collection->setPageSize($limit)
                ->setCurPage(1);

        $collection->getSelect()->group("e.entity_id");
        $collection->getSelect()->order("rand()")->where('price_index.final_price < price_index.price');
        return $collection;
    }
}
