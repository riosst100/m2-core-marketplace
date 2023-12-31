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
namespace Lofmp\FavoriteSeller\Block\Suggestion;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

class ShowSuggestion extends ListProduct {
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
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

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
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory $sellerProductCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Lofmp\FavoriteSeller\Helper\ConfigData $moduleConfigData
     * @param \Lofmp\FavoriteSeller\Model\Config\Source\SuggestionCriteria $suggestionCriteria
     * @param array $data
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory $sellerProductCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Lofmp\FavoriteSeller\Helper\ConfigData $moduleConfigData,
        \Lofmp\FavoriteSeller\Model\Config\Source\SuggestionCriteria $suggestionCriteria,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->sellerProductCollectionFactory = $sellerProductCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->moduleConfigData = $moduleConfigData;
        $this->suggestionCriteria = $suggestionCriteria;
        $this->reviewFactory = $reviewFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->_localeDate               = $localeDate;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return $this
     */
    public function _beforeToHtml()
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

        $suggestionCollection = null;
        switch ($orderBy){
            case $this->suggestionCriteria::TOP_RATED :
                $suggestionCollection = $this->getTopRatedProducts($sellerIds);
                break;
            case $this->suggestionCriteria::FEATURED:
                $suggestionCollection = $this->getFeaturedProducts($sellerIds);
                break;
            case $this->suggestionCriteria::NEW_ARRIVAL:
                $suggestionCollection = $this->getNewArrivalProducts($sellerIds);
                break;
            case $this->suggestionCriteria::RANDOM:
                $suggestionCollection = $this->getRandomProducts($sellerIds);
                break;
            case $this->suggestionCriteria::DEALS:
                $suggestionCollection = $this->getDealsProducts($sellerIds);
                break;
            case $this->suggestionCriteria::CREATED_AT :
            default:
                $suggestionCollection = $this->getLastedProducts($sellerIds);
                break;
        }
        if($suggestionCollection){
            $suggestionCollection->load();
        }
        $this->setProductCollection($suggestionCollection);

        return parent::_beforeToHtml();
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
     */
    public function setProductCollection(AbstractCollection $collection)
    {
        $this->setCollection($collection);
    }


    /**
     * Get Lasted Products
     * @param int[]|null $sellerIds
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getLastedProducts($sellerIds){
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->setOrder('created_at', 'DESC');

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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getTopRatedProducts($sellerIds){
        $storeId = $this->_storeManager->getStore(true)->getId();
        $_resource = $this->reviewFactory->create()->getResource();
        $sellerProductTable = $_resource->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('status', ['eq' => 1])
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addAttributeToSelect('*')
            ->addStoreFilter()
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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getFeaturedProducts($sellerIds){
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(array(array( 'attribute'=>'featured', 'eq' => '1')))
            ->addStoreFilter()
            ->setOrder('created_at', 'DESC');

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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getNewArrivalProducts($sellerIds){
        $todayStartOfDayDate = $this->_localeDate->date()
            ->setTime(0, 0)
            ->format('Y-m-d H:i:s');

        $todayEndOfDayDate = $this->_localeDate->date()
            ->setTime(23, 59, 59)
            ->format('Y-m-d H:i:s');

        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
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

        $collection->setOrder('created_at', 'DESC');

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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getRandomProducts($sellerIds){
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['in' => $sellerIds])
            ->addFieldToFilter('status', ['eq' => 1])
            ->addAttributeToSelect('*')
            ->addStoreFilter();

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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getDealsProducts($sellerIds){
        $todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $sellerProductTable = $this->reviewFactory->create()->getResource()->getTable("lof_marketplace_product");
        $collection = $this->productCollectionFactory->create()
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

        $collection->getSelect()->group("e.entity_id");
        $collection->getSelect()->order("rand()")->where('price_index.final_price < price_index.price');
        return $collection;
    }
}
