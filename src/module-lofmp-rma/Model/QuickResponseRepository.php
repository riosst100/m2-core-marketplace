<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\QuickResponseRepositoryInterface;
use Lofmp\Rma\Api\Data\QuickResponseSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\QuickResponseInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\QuickResponse as ResourceQuickResponse;
use Lofmp\Rma\Model\ResourceModel\QuickResponse\CollectionFactory as QuickResponseCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class QuickResponseRepository implements quickResponseRepositoryInterface
{

    protected $resource;

    protected $quickResponseFactory;

    protected $quickResponseCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataQuickResponseFactory;

    private $storeManager;


    /**
     * @param ResourceQuickResponse $resource
     * @param QuickResponseFactory $quickResponseFactory
     * @param QuickResponseInterfaceFactory $dataQuickResponseFactory
     * @param QuickResponseCollectionFactory $quickResponseCollectionFactory
     * @param QuickResponseSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceQuickResponse $resource,
        QuickResponseFactory $quickResponseFactory,
        QuickResponseInterfaceFactory $dataQuickResponseFactory,
        QuickResponseCollectionFactory $quickResponseCollectionFactory,
        QuickResponseSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->quickResponseFactory = $quickResponseFactory;
        $this->quickResponseCollectionFactory = $quickResponseCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuickResponseFactory = $dataQuickResponseFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\QuickResponseInterface $quickResponse
    ) {
        /* if (empty($quickResponse->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $quickResponse->setStoreId($storeId);
        } */
        try {
            $quickResponse->getResource()->save($quickResponse);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quickResponse: %1',
                $exception->getMessage()
            ));
        }
        return $quickResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($quickResponseId)
    {
        $quickResponse = $this->quickResponseFactory->create();
        $quickResponse->getResource()->load($quickResponse, $quickResponseId);
        if (!$quickResponse->getId()) {
            throw new NoSuchEntityException(__('QuickResponse with id "%1" does not exist.', $quickResponseId));
        }
        return $quickResponse;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->quickResponseCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\QuickResponseInterface $quickResponse
    ) {
        try {
            $quickResponse->getResource()->delete($quickResponse);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the QuickResponse: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quickResponseId)
    {
        return $this->delete($this->getById($quickResponseId));
    }
}
