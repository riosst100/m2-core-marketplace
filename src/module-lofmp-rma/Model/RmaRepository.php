<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\RmaRepositoryInterface;
use Lofmp\Rma\Api\Data\RmaSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\RmaInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Rma as ResourceRma;
use Lofmp\Rma\Model\ResourceModel\Rma\CollectionFactory as RmaCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RmaRepository implements rmaRepositoryInterface
{

    protected $resource;

    protected $rmaFactory;

    protected $rmaCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataRmaFactory;

    private $storeManager;


    /**
     * @param ResourceRma $resource
     * @param RmaFactory $rmaFactory
     * @param RmaInterfaceFactory $dataRmaFactory
     * @param RmaCollectionFactory $rmaCollectionFactory
     * @param RmaSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRma $resource,
        RmaFactory $rmaFactory,
        RmaInterfaceFactory $dataRmaFactory,
        RmaCollectionFactory $rmaCollectionFactory,
        RmaSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->rmaFactory = $rmaFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRmaFactory = $dataRmaFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        /* if (empty($rma->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $rma->setStoreId($storeId);
        } */
        try {
            $rma->getResource()->save($rma);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the rma: %1',
                $exception->getMessage()
            ));
        }
        return $rma;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($rmaId)
    {
        $rma = $this->rmaFactory->create();
        $rma->getResource()->load($rma, $rmaId);
        if (!$rma->getId()) {
            throw new NoSuchEntityException(__('rma with id "%1" does not exist.', $rmaId));
        }
        return $rma;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->rmaCollectionFactory->create();
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
    public function delete(\Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        try {
            $rma->getResource()->delete($rma);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the rma: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($rmaId)
    {
        return $this->delete($this->getById($rmaId));
    }
}
