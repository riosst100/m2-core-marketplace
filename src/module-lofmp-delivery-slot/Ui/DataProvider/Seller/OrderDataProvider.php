<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Ui\DataProvider\Seller;

use Lof\MarketPlace\Model\ResourceModel\Order\CollectionFactory;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\SessionFactory;

class OrderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Seller Order collection
     *
     * @var \Lof\MarketPlace\Model\ResourceModel\Order\CollectionFactory
     */
    protected $collection;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var SessionFactory
     */
    protected $sessionFactory;


    /**
     * OrderDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Session $sessionFactory
     * @param SellerFactory $sellerFactory
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
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->sessionFactory = $sessionFactory;
        $this->sellerFactory = $sellerFactory;
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->setSellerToFilter();
            $this->setDefaultStatusToFilter();
            $this->joinOrderGridTable();
            $this->setDefaultSortOrder();
            $this->getCollection()->load();
        }

        $items = $this->getCollection()->toArray();

        return [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => array_values($items['items']),
        ];
    }

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

    public function setDefaultStatusToFilter()
    {
        $availableStatus = [
            "pending",
            "processing"
        ];
        $this->getCollection()->addFieldToFilter('main_table.status', [
            "in" => $availableStatus
        ]);
        return $this; 
    }

    public function setDefaultSortOrder()
    {
        $this->getCollection()->addOrder("main_table.increment_id","DESC");
        return $this; 
    }

    public function joinOrderGridTable()
    {
        //join table sales_order_grid, sales_order
        $this->getCollection()
            ->getSelect()
            ->join(
                ['order_grid_table' => $this->getCollection()->getResource()->getTable("sales_order_grid")],
                'main_table.order_id = order_grid_table.entity_id',
                [
                    'shipping_information', 
                    'store_id', 
                    'store_name', 
                    'shipping_name', 
                    'billing_name', 
                    'created_at', 
                    'updated_at', 
                    'billing_address', 
                    'shipping_address', 
                    'customer_name',
                    'base_grand_total',
                    'grand_total',
                    'subtotal',
                    'shipping_and_handling'
                ]
            )
            // ->join(
            //     ['order_table' => $this->getCollection()->getResource()->getTable("sales_order")],
            //     'main_table.order_id = order_table.entity_id',
            //     [
            //         'delivery_time_slot' => 'order_delivery_time_slot', 
            //         'delivery_slot_id' => 'order_delivery_slot_id',
            //         'delivery_comment' => 'order_delivery_comment'
            //     ]
            // )
            ->where('main_table.delivery_slot_id > 0')
            ->group(
                'main_table.order_id'
            );
        
        return $this;
    }

    /**
     * Add field to select
     *
     * @param string|array $field
     * @param string|null $alias
     * @return void
     */
    public function addField($field, $alias = null)
    {
        if (isset($this->addFieldStrategies[$field])) {
            $this->addFieldStrategies[$field]->addField($this->getCollection(), $field, $alias);
        } else {
            parent::addField($field, $alias);
        }
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (isset($this->addFilterStrategies[$filter->getField()])) {
            $this->addFilterStrategies[$filter->getField()]
                ->addFilter(
                    $this->getCollection(),
                    $filter->getField(),
                    [$filter->getConditionType() => $filter->getValue()]
                );
        } else {
            parent::addFilter($filter);
        }
    }
}
