<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FeaturedProducts
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\FeaturedProducts\Model;

use Lof\MarketPlace\Model\SellerProduct;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Product extends \Magento\Framework\DataObject
{
    /**
     * Page cache tag
     */
    const CACHE_TAG = 'lofmp_featuredproduct';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * \Magento\Framework\App\ResourceConnection
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * Catalog inventory data
     *
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    protected $stockConfiguration = null;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility                      $catalogProductVisibility
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $localeDate
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                    $date
     * @param \Magento\Framework\App\ResourceConnection                      $resource
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State              $productState
     * @param \Magento\Catalog\Model\ProductFactory                          $productFactory
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface      $stockConfiguration
     * @param \Magento\CatalogInventory\Helper\Stock                         $stockFilter
     * @param array                                                          $data
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $productState,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        array $data = []
        ) {
        $this->_localeDate               = $localeDate;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager             = $storeManager;
        $this->dateTime                      = $date;
        $this->_resource                 = $resource;
        $this->productState              = $productState;
        $this->productFactory            = $productFactory;
        $this->stockConfiguration        = $stockConfiguration;
        $this->stockFilter               = $stockFilter;
        parent::__construct($data);
    }

    /**
     * Get date time
     *
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Get timezone date time
     *
     * @param string $dateTime = "today"
     * @return string
     */
    public function getTimezoneDateTime($dateTime = "today")
    {
        if ($dateTime === "today" || !$dateTime) {
            $dateTime = $this->getDateTime()->gmtDate();
        }

        $today = $this->_localeDate
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * get featured product collection
     *
     * @param int $sellerId
     * @param int $pageSize
     * @param int $currentPage
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection|null
     */
    public function getCollection($sellerId, $pageSize = 0, $currentPage = 0)
    {
        $currentDate = $this->getTimezoneDateTime();

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $resource = $collection->getResource();
        $collection->addAttributeToSelect('*')
            ->joinTable(
                ['featured_product' => $resource->getTable('lofmp_featuredproducts_product')],
                'product_id = entity_id',
                [
                    'seller_id' => 'seller_id',
                    'featured_from' => 'featured_from',
                    'featured_to' => 'featured_to',
                    'sort_order' => 'sort_order'
                ]
            )
            ->addFieldToFilter('seller_id', ['eq' => $sellerId])
            ->addFieldToFilter('featured_from', [
                ['lteq' => $currentDate],
                ['null' => true]
            ])
            ->addFieldToFilter('featured_to', [
                ['gteq' => $currentDate],
                ['null' => true]
            ])
            ->setOrder('sort_order', 'ASC');

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToSelect('*')
                ->addStoreFilter()
                ->addAttributeToFilter('approval',
                    ['in' =>
                        [
                            SellerProduct::STATUS_NOT_SUBMITED,
                            SellerProduct::STATUS_APPROVED
                        ]
                    ]
                );

        if ($pageSize) {
            $collection->setPageSize((int)$pageSize);
        }

        if ($currentPage) {
            $collection->setCurPage((int)$currentPage);
        }

        $collection->getSelect()->group("e.entity_id");
        return $collection;
    }
}
