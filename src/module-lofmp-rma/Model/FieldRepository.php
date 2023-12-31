<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\FieldRepositoryInterface;
use Lofmp\Rma\Api\Data\FieldSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\FieldInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Field as ResourceField;
use Lofmp\Rma\Model\ResourceModel\Field\CollectionFactory as FieldCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class FieldRepository implements fieldRepositoryInterface
{

    protected $resource;

    protected $fieldFactory;

    protected $fieldCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataFieldFactory;

    private $storeManager;


    /**
     * @param ResourceField $resource
     * @param FieldFactory $fieldFactory
     * @param FieldInterfaceFactory $dataFieldFactory
     * @param FieldCollectionFactory $fieldCollectionFactory
     * @param FieldSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceField $resource,
        FieldFactory $fieldFactory,
        FieldInterfaceFactory $dataFieldFactory,
        FieldCollectionFactory $fieldCollectionFactory,
        FieldSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->fieldFactory = $fieldFactory;
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFieldFactory = $dataFieldFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\FieldInterface $field
    ) {
        /* if (empty($field->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $field->setStoreId($storeId);
        } */
        try {
            $field->getResource()->save($field);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the field: %1',
                $exception->getMessage()
            ));
        }
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fieldId)
    {
        $field = $this->fieldFactory->create();
        $field->getResource()->load($field, $fieldId);
        if (!$field->getId()) {
            throw new NoSuchEntityException(__('field with id "%1" does not exist.', $fieldId));
        }
        return $field;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->fieldCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\FieldInterface $field
    ) {
        try {
            $field->getResource()->delete($field);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the field: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fieldId)
    {
        return $this->delete($this->getById($fieldId));
    }
}
