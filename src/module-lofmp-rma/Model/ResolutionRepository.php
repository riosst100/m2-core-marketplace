<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\ResolutionRepositoryInterface;
use Lofmp\Rma\Api\Data\ResolutionSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\ResolutionInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Resolution as ResourceResolution;
use Lofmp\Rma\Model\ResourceModel\Resolution\CollectionFactory as ResolutionCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ResolutionRepository implements resolutionRepositoryInterface
{

    protected $resource;

    protected $resolutionFactory;

    protected $resolutionCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataResolutionFactory;

    private $storeManager;


    /**
     * @param ResourceResolution $resource
     * @param ResolutionFactory $resolutionFactory
     * @param ResolutionInterfaceFactory $dataResolutionFactory
     * @param ResolutionCollectionFactory $resolutionCollectionFactory
     * @param ResolutionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceResolution $resource,
        ResolutionFactory $resolutionFactory,
        ResolutionInterfaceFactory $dataResolutionFactory,
        ResolutionCollectionFactory $resolutionCollectionFactory,
        ResolutionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->resolutionFactory = $resolutionFactory;
        $this->resolutionCollectionFactory = $resolutionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataResolutionFactory = $dataResolutionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\ResolutionInterface $resolution
    ) {
        /* if (empty($resolution->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $resolution->setStoreId($storeId);
        } */
        try {
            $resolution->getResource()->save($resolution);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the resolution: %1',
                $exception->getMessage()
            ));
        }
        return $resolution;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($resolutionId)
    {
        $resolution = $this->resolutionFactory->create();
        $resolution->getResource()->load($resolution, $resolutionId);
        if (!$resolution->getId()) {
            throw new NoSuchEntityException(__('resolution with id "%1" does not exist.', $resolutionId));
        }
        return $resolution;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->resolutionCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\ResolutionInterface $resolution
    ) {
        try {
            $resolution->getResource()->delete($resolution);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the resolution: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($resolutionId)
    {
        return $this->delete($this->getById($resolutionId));
    }
}
