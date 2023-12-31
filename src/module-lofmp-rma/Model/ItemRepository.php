<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\ItemRepositoryInterface;
use Lofmp\Rma\Api\Data\ItemSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\ItemInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Item as ResourceItem;
use Lofmp\Rma\Model\ResourceModel\Item\CollectionFactory as ItemCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class ItemRepository implements itemRepositoryInterface
{

    protected $resource;

    protected $itemFactory;

    protected $itemCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataItemFactory;

    private $storeManager;


    /**
     * @param ResourceItem $resource
     * @param ItemFactory $itemFactory
     * @param ItemInterfaceFactory $dataItemFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param ItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceItem $resource,
        ItemFactory $itemFactory,
        ItemInterfaceFactory $dataItemFactory,
        ItemCollectionFactory $itemCollectionFactory,
        ItemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->itemFactory = $itemFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataItemFactory = $dataItemFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Lofmp\Rma\Api\Data\ItemInterface $item)
    {
        /* if (empty($item->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $item->setStoreId($storeId);
        } */
        try {
            $item->getResource()->save($item);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the item: %1',
                $exception->getMessage()
            ));
        }
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($itemId)
    {
        $item = $this->itemFactory->create();
        $item->getResource()->load($item, $itemId);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $itemId));
        }
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->itemCollectionFactory->create();
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
    public function delete(\Lofmp\Rma\Api\Data\ItemInterface $item)
    {
        try {
            $item->getResource()->delete($item);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Item: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($itemId)
    {
        return $this->delete($this->getById($itemId));
    }
}
