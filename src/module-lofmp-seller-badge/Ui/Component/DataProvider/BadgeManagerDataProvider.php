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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Ui\Component\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\CollectionFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class BadgeManagerDataProvider extends DataProvider
{
    /**
     * @var \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager\Collection
     */
    protected $collection;

    /**
     * BadgeManagerDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
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
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->collection->getSelect()->joinInner(
            ['lms' => $this->collection->getTable('lof_marketplace_seller')],
            'main_table.seller_id = lms.seller_id',
            ['lms.name as seller_name', 'lms.email']
        )->joinLeft(
            ['lsb' => $this->collection->getTable('lofmp_sellerbadge_badge')],
            'main_table.badge_id = lsb.badge_id',
            ['lsb.name as badge_name', 'lsb.image as image']
        );
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        return $this->getCollection()->toArray();
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}
