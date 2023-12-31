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

namespace Lof\MarketPlace\Ui\DataProvider\Marketplace;

use Magento\Sales\Model\ResourceModel\Order\Shipment\Grid\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Grid\Collection;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\RequestInterface;

class ShipmentDataProvider extends DataProvider
{
   /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]|mixed
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]|mixed
     */
    protected $addFilterStrategies;

    /**
     * Shipment collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Grid\Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var bool
     */
    protected $flagFiltered = false;

    /**
     * ShipmentDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param SessionFactory $sessionFactory
     * @param SellerFactory $sellerFactory
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        SessionFactory $sessionFactory,
        SellerFactory $sellerFactory,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->collection = $collectionFactory->create();
        $this->sessionFactory = $sessionFactory;
        $this->sellerFactory = $sellerFactory;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->joinSellerOrderTable();
            $this->setSellerToFilter();
            $this->setDefaultSortOrder();
            $this->getCollection()->load();
            $this->flagFiltered = true;
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        //TODO: Technical dept, should be implemented as part of SearchAPI support for Catalog Grids
        if (!$this->flagFiltered) {
            $this->getCollection()->getSelect()->columns("*");
            $this->joinSellerOrderTable();
            $this->setSellerToFilter();
            $this->setDefaultSortOrder();
            $this->flagFiltered = true;
        }
        return parent::getSearchResult();
    }

    /**
     * Set Seller to Filter
     *
     * @return $this
     */
    public function setSellerToFilter()
    {
        $customerId = $this->sessionFactory->create()->getId();
        $seller = $this->sellerFactory->create()
            ->load($customerId, 'customer_id');
        $sellerId = $seller->getId();
        if ($sellerId) {
            $this->getCollection()->addFieldToFilter('seller_order_table.seller_id', $sellerId);
        }
        return $this;
    }

    /**
     * Set default sort order
     *
     * @return $this
     */
    public function setDefaultSortOrder()
    {
        //$this->getCollection()->addOrder("main_table.increment_id", "DESC");
        return $this;
    }

    /**
     * Join shipment grid table
     *
     * @return $this
     */
    public function joinSellerOrderTable()
    {
        $this->getCollection()
            ->getSelect()
            ->join(
                ['seller_order_table' => $this->getCollection()->getResource()->getTable("lof_marketplace_sellerorder")],
                'main_table.order_id = seller_order_table.order_id',
                [
                    'seller_id',
                    'status',
                    'customer_id'
                ]
            );

        return $this;
    }

    /**
     * get mapping fields
     *
     * @return mixed|array
     */
    protected function getMappingFields()
    {
        $mappingField = [
            "status" => "seller_order_table.status",
            "increment_id" => "main_table.increment_id",
            "customer_id" => "seller_order_table.customer_id",
            "entity_id" => "main_table.entity_id"
        ];
        return $mappingField;
    }

}
