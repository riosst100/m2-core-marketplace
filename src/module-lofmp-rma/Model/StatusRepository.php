<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\StatusRepositoryInterface;
use Lofmp\Rma\Api\Data\StatusSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\StatusInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Status as ResourceStatus;
use Lofmp\Rma\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class StatusRepository implements statusRepositoryInterface
{

    protected $resource;

    protected $statusFactory;

    protected $statusCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataStatusFactory;

    private $storeManager;


    /**
     * @param ResourceStatus $resource
     * @param StatusFactory $statusFactory
     * @param StatusInterfaceFactory $dataStatusFactory
     * @param StatusCollectionFactory $statusCollectionFactory
     * @param StatusSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceStatus $resource,
        StatusFactory $statusFactory,
        StatusInterfaceFactory $dataStatusFactory,
        StatusCollectionFactory $statusCollectionFactory,
        StatusSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->statusFactory = $statusFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataStatusFactory = $dataStatusFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\StatusInterface $status
    ) {
        /* if (empty($status->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $status->setStoreId($storeId);
        } */
        try {
            $status->getResource()->save($status);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the status: %1',
                $exception->getMessage()
            ));
        }
        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($statusId)
    {
        $status = $this->statusFactory->create();
        $status->getResource()->load($status, $statusId);
        if (!$status->getId()) {
            throw new NoSuchEntityException(__('Status with id "%1" does not exist.', $statusId));
        }
        return $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->statusCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\StatusInterface $status
    ) {
        try {
            $status->getResource()->delete($status);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Status: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($statusId)
    {
        return $this->delete($this->getById($statusId));
    }
}
