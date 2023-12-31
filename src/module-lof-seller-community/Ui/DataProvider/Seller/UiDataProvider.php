<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_SellerCommunity
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SellerCommunity\Ui\DataProvider\Seller;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Lof\MarketPlace\Ui\DataProvider\Marketplace\DataProvider;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\ObjectManagerInterface;

class UiDataProvider extends DataProvider
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
     * Category collection
     *
     * @var AbstractCollection
     */
    protected $collection;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var string
     */
    protected $_collectionClass;

    /**
     * @var string
     */
    protected $sellerIdField = "seller_id";

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * ShipmentDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param SessionFactory $sessionFactory
     * @param SellerFactory $sellerFactory
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param ObjectManagerInterface $objectManager
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        SessionFactory $sessionFactory,
        SellerFactory $sellerFactory,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        ObjectManagerInterface $objectManager,
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

        $this->sessionFactory = $sessionFactory;
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->sellerFactory = $sellerFactory;
        $this->_objectManager = $objectManager;
        $this->initDataCollection();
    }

    /**
     * init collection
     * @return $this
     */
    protected function initDataCollection()
    {
        if (!empty($this->_collectionClass)) {
            $this->collection = $this->_objectManager->create($this->_collectionClass);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->setSellerToFilter();
            $this->getCollection()->load();
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
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
            $this->getCollection()->addFieldToFilter($this->sellerIdField, $sellerId);
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
        return $this;
    }
}
