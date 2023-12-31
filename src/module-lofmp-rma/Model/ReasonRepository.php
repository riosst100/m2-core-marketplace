<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\ReasonRepositoryInterface;
use Lofmp\Rma\Api\Data\ReasonSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\ReasonInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Reason as ResourceReason;
use Lofmp\Rma\Model\ResourceModel\Reason\CollectionFactory as ReasonCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ReasonRepository implements reasonRepositoryInterface
{

    protected $resource;

    protected $reasonFactory;

    protected $reasonCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataReasonFactory;

    private $storeManager;


    /**
     * @param ResourceReason $resource
     * @param ReasonFactory $reasonFactory
     * @param ReasonInterfaceFactory $dataReasonFactory
     * @param ReasonCollectionFactory $reasonCollectionFactory
     * @param ReasonSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceReason $resource,
        ReasonFactory $reasonFactory,
        ReasonInterfaceFactory $dataReasonFactory,
        ReasonCollectionFactory $reasonCollectionFactory,
        ReasonSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->reasonFactory = $reasonFactory;
        $this->reasonCollectionFactory = $reasonCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataReasonFactory = $dataReasonFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\ReasonInterface $reason
    ) {
        /* if (empty($reason->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $reason->setStoreId($storeId);
        } */
        try {
            $reason->getResource()->save($reason);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the reason: %1',
                $exception->getMessage()
            ));
        }
        return $reason;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($reasonId)
    {
        $reason = $this->reasonFactory->create();
        $reason->getResource()->load($reason, $reasonId);
        if (!$reason->getId()) {
            throw new NoSuchEntityException(__('reason with id "%1" does not exist.', $reasonId));
        }
        return $reason;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->reasonCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\ReasonInterface $reason
    ) {
        try {
            $reason->getResource()->delete($reason);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the reason: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($reasonId)
    {
        return $this->delete($this->getById($reasonId));
    }
}
