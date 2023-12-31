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

use Lof\MarketPlace\Model\ResourceModel\Invoice\Grid\CollectionFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\RequestInterface;

class InvoiceDataProvider extends DataProvider
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
     * Product collection
     *
     * @var \Lof\MarketPlace\Model\ResourceModel\Amounttransaction\Collection
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
     * InvoiceDataProvider constructor.
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
            $this->getCollection()->getSelect()->columns("*");
            $this->setSellerToFilter();
            //$this->joinInvoiceGridTable();
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
            $this->setSellerToFilter();
            //$this->joinInvoiceGridTable();
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
            $this->getCollection()->addFieldToFilter('main_table.seller_id', $sellerId);
        }
        return $this;
    }

    /**
     * Join order grid table
     *
     * @return $this
     */
    public function joinInvoiceGridTable()
    {
        $this->getCollection()
            ->getSelect()
            ->join(
                [
                    // @phpstan-ignore-next-line
                    'invoice_grid' => $this->getCollection()->getResource()->getTable('sales_invoice_grid')
                ],
                'main_table.invoice_id=invoice_grid.entity_id',
                [
                    'entity_id',
                    'increment_id',
                    'order_increment_id',
                    'order_id',
                    'store_id',
                    'customer_name',
                    'billing_name',
                    'billing_address',
                    'shipping_address',
                    'store_currency_code',
                    'order_currency_code',
                    'base_currency_code',
                    'global_currency_code',
                    'base_grand_total',
                    'grand_total',
                    'created_at',
                    'state'
                ]
            )
            ->group(
                'main_table.invoice_id'
            );

        return $this;
    }

    /**
     * Set default sort order
     *
     * @return $this
     */
    public function setDefaultSortOrder()
    {
        //$this->getCollection()->addOrder("main_table.created_at","DESC");
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
            "status" => "main_table.status",
            "increment_id" => "invoice_grid.increment_id",
            "customer_id" => "invoice_grid.customer_id",
            "order_id" => "invoice_grid.order_id",
            "base_grand_total" => "invoice_grid.base_grand_total",
            "grand_total" => "invoice_grid.grand_total",
            "customer_name" => "invoice_grid.customer_name",
            "state" => "invoice_grid.state",
            "created_at" => "main_table.created_at"
        ];
        return $mappingField;
    }
}
