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
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Ui\DataProvider\Seller;

use Lof\MarketPlace\Helper\Data;
use Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;

class SubscriptionDataProvider extends DataProvider
{
    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\Collection
     */
    protected $collection;

    /**
     * @var Data
     */
    protected $helper;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
        Data $helper,
        array $meta = [],
        array $data = []
    ) {
        $this->helper = $helper;
        $this->collection = $collectionFactory->create()
            ->addFieldToFilter('seller_id', $this->helper->getSellerId());
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
