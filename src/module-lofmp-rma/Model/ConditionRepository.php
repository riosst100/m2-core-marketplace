<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\ConditionRepositoryInterface;
use Lofmp\Rma\Api\Data\ConditionSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\ConditionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Condition as ResourceCondition;
use Lofmp\Rma\Model\ResourceModel\Condition\CollectionFactory as ConditionCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ConditionRepository implements conditionRepositoryInterface
{

    protected $resource;

    protected $conditionFactory;

    protected $conditionCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataConditionFactory;

    private $storeManager;


    /**
     * @param ResourceCondition $resource
     * @param ConditionFactory $conditionFactory
     * @param ConditionInterfaceFactory $dataConditionFactory
     * @param ConditionCollectionFactory $conditionCollectionFactory
     * @param ConditionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceCondition $resource,
        ConditionFactory $conditionFactory,
        ConditionInterfaceFactory $dataConditionFactory,
        ConditionCollectionFactory $conditionCollectionFactory,
        ConditionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->conditionFactory = $conditionFactory;
        $this->conditionCollectionFactory = $conditionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataConditionFactory = $dataConditionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\ConditionInterface $condition
    ) {
        /* if (empty($condition->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $condition->setStoreId($storeId);
        } */
        try {
            $condition->getResource()->save($condition);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the condition: %1',
                $exception->getMessage()
            ));
        }
        return $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($conditionId)
    {
        $condition = $this->conditionFactory->create();
        $condition->getResource()->load($condition, $conditionId);
        if (!$condition->getId()) {
            throw new NoSuchEntityException(__('condition with id "%1" does not exist.', $conditionId));
        }
        return $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->conditionCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\ConditionInterface $condition
    ) {
        try {
            $condition->getResource()->delete($condition);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the condition: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($conditionId)
    {
        return $this->delete($this->getById($conditionId));
    }
}
