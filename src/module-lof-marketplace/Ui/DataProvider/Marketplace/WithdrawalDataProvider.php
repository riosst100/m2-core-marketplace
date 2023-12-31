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

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\SessionFactory;

class WithdrawalDataProvider extends \Lof\MarketPlace\Ui\DataProvider\Seller\WithdrawalDataProvider
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
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Lof\MarketPlace\Model\ResourceModel\Withdrawal\CollectionFactory $collectionFactory
     * @param SessionFactory $sessionFactory
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
        \Lof\MarketPlace\Model\ResourceModel\Withdrawal\CollectionFactory $collectionFactory,
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
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->sessionFactory = $sessionFactory;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->setSellerToFilter();
            $this->setDefaultSortOrder();
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
            $this->getCollection()->addFieldToFilter('main_table.seller_id', $sellerId);
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
        $this->getCollection()->addOrder('main_table.withdrawal_id', 'DESC');
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
