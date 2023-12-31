<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\OrderStatusHistoryRepositoryInterface;
use Lofmp\Rma\Api\Data\OrderStatusHistorySearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\OrderStatusHistoryInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\OrderStatusHistory as ResourceOrderStatusHistory;
use Lofmp\Rma\Model\ResourceModel\OrderStatusHistory\CollectionFactory as OrderStatusHistoryCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class OrderStatusHistoryRepository implements orderStatushistoryRepositoryInterface
{

    protected $resource;

    protected $orderStatushistoryFactory;

    protected $orderStatushistoryCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataOrderStatusHistoryFactory;

    private $storeManager;


    /**
     * @param ResourceOrderStatusHistory $resource
     * @param OrderStatusHistoryFactory $orderStatushistoryFactory
     * @param OrderStatusHistoryInterfaceFactory $dataOrderStatusHistoryFactory
     * @param OrderStatusHistoryCollectionFactory $orderStatushistoryCollectionFactory
     * @param OrderStatusHistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceOrderStatusHistory $resource,
        OrderStatusHistoryFactory $orderStatushistoryFactory,
        OrderStatusHistoryInterfaceFactory $dataOrderStatusHistoryFactory,
        OrderStatusHistoryCollectionFactory $orderStatushistoryCollectionFactory,
        OrderStatusHistorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->orderStatushistoryFactory = $orderStatushistoryFactory;
        $this->orderStatushistoryCollectionFactory = $orderStatushistoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataOrderStatusHistoryFactory = $dataOrderStatusHistoryFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\OrderStatusHistoryInterface $orderStatushistory
    ) {
        /* if (empty($orderStatushistory->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $orderStatushistory->setStoreId($storeId);
        } */
        try {
            $orderStatushistory->getResource()->save($orderStatushistory);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the orderStatushistory: %1',
                $exception->getMessage()
            ));
        }
        return $orderStatushistory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($orderStatushistoryId)
    {
        $orderStatushistory = $this->orderStatushistoryFactory->create();
        $orderStatushistory->getResource()->load($orderStatushistory, $orderStatushistoryId);
        if (!$orderStatushistory->getId()) {
            throw new NoSuchEntityException(__('OrderStatusHistory with id "%1" does not exist.', $orderStatushistoryId));
        }
        return $orderStatushistory;
    }
   

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->orderStatushistoryCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lofmp\Rma\Api\Data\OrderStatusHistoryInterface $orderStatushistory
    ) {
        try {
            $orderStatushistory->getResource()->delete($orderStatushistory);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the OrderStatusHistory: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($orderStatushistoryId)
    {
        return $this->delete($this->getById($orderStatushistoryId));
    }
}
