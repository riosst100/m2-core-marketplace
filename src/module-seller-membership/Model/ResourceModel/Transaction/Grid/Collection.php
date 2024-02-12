<?php

namespace CoreMarketplace\SellerMembership\Model\ResourceModel\Transaction\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Lofmp\SellerMembership\Model\ResourceModel\Transaction\Collection as TransactionCollection;

/**
 * Collection for displaying grid of cms blocks
 */
class Collection extends \Lofmp\SellerMembership\Model\ResourceModel\Transaction\Grid\Collection
{
    protected $helper;

    public function __construct(
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPlace\Helper\Data $helper,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $entityFactory,
            $logger,
            $mainTable,
            $eventPrefix,
            $eventObject,
            $resourceModel,
            $model,
            $connection,
            $resource
        );

        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->helper = $helper;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);

        $this->addFieldToFilter('seller_id', $this->helper->getSellerId());
    }
}
