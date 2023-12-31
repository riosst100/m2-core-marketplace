<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\AddressRepositoryInterface;
use Lofmp\Rma\Api\Data\AddressSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\AddressInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Address as ResourceAddress;
use Lofmp\Rma\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class AddressRepository implements addressRepositoryInterface
{

    protected $resource;

    protected $addressFactory;

    protected $addressCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataAddressFactory;

    private $storeManager;


    /**
     * @param ResourceAddress $resource
     * @param AddressFactory $addressFactory
     * @param AddressInterfaceFactory $dataAddressFactory
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param AddressSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceAddress $resource,
        AddressFactory $addressFactory,
        AddressInterfaceFactory $dataAddressFactory,
        AddressCollectionFactory $addressCollectionFactory,
        AddressSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->addressFactory = $addressFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataAddressFactory = $dataAddressFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\AddressInterface $address
    ) {
        /* if (empty($address->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $address->setStoreId($storeId);
        } */
        try {
            $address->getResource()->save($address);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the address: %1',
                $exception->getMessage()
            ));
        }
        return $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($addressId)
    {
        $address = $this->addressFactory->create();
        $address->getResource()->load($address, $addressId);
        if (!$address->getId()) {
            throw new NoSuchEntityException(__('address with id "%1" does not exist.', $addressId));
        }
        return $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->addressCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\AddressInterface $address
    ) {
        try {
            $address->getResource()->delete($address);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the address: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($addressId)
    {
        return $this->delete($this->getById($addressId));
    }
}
