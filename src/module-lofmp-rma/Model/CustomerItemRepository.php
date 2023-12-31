<?php
/**
 * LandOfCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\CustomerItemRepositoryInterface;
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
use Lofmp\Rma\Model\RmaFactory as RmaModelFactory;
use Magento\Store\Model\StoreManagerInterface;

class CustomerItemRepository implements CustomerItemRepositoryInterface
{
    /**
     * @var ResourceItem
     */
    protected $resource;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var ItemInterfaceFactory
     */
    protected $itemCollectionFactory;

    /**
     * @var ItemSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    
    /**
     * @var ItemInterfaceFactory
     */
    protected $dataItemFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var RmaModelFactory
     */
    private $rmaModelFactory;


    /**
     * @param ResourceItem $resource
     * @param ItemFactory $itemFactory
     * @param ItemInterfaceFactory $dataItemFactory
     * @param ItemCollectionFactory $itemCollectionFactory
     * @param ItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param RmaModelFactory $rmaModelFactory
     */
    public function __construct(
        ResourceItem $resource,
        ItemFactory $itemFactory,
        ItemInterfaceFactory $dataItemFactory,
        ItemCollectionFactory $itemCollectionFactory,
        ItemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        RmaModelFactory $rmaModelFactory
    ) {
        $this->resource = $resource;
        $this->itemFactory = $itemFactory;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataItemFactory = $dataItemFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->rmaModelFactory = $rmaModelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save($customerId, \Lofmp\Rma\Api\Data\ItemInterface $item)
    {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        try {
            $rmaId = $item->getRmaId();
            $orderItemId = $item->getOrderItemId();
            $productId = $item->getProductId();
            if($rmaId && $orderItemId && $productId){
                $rmaModel = $this->rmaModelFactory->create()->load((int)$rmaId);
                if($rmaModel->getId() && ($customerId == $rmaModel->getCustomerId())){
                    $item->getResource()->save($item);
                }else{
                    throw new NoSuchEntityException(__('RMA Request with ID %1 is not exists.', $rmaId));
                }
                
            }else {
                throw new NoSuchEntityException(__('Require RMA ID and Order Item Id and Product ID.'));
            }
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
    public function getById($customerId, $itemId)
    {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        $item = $this->itemFactory->create();
        $item->getResource()->load($item, $itemId);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $itemId));
        }
        $rmaId = $item->getRmaId();
        $rmaModel = $this->rmaModelFactory->create()->load((int)$rmaId);
        if(!$rmaModel->getId() || ($customerId != $rmaModel->getCustomerId())){
            throw new NoSuchEntityException(__('RMA Request with ID %1 is not exists.', $rmaId));
        }
        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemByRma(
        $customerId,
        $rmaId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        $rma = $this->rmaModelFactory->create()->load((int)$rmaId);
        if($rma->getId()){
            $rma_ids = $rma->getChildIds();
            $collection = $this->itemCollectionFactory->create();
            $collection->joinRmaTable();
            $rma_ids = is_array($rma_ids)?$rma_ids:[];
            $rma_ids[] = $rma->getId();

            $collection->addFieldToFilter("main_table.rma_id", ["in" => $rma_ids]);
            $collection->addFieldToFilter("rma.customer_id", $customerId);

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
        }else {
            throw new CouldNotDeleteException(__(
                'Could not load Items because RMA is not exists.'
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $customerId, 
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        $collection = $this->itemCollectionFactory->create();
        $collection->joinRmaTable();
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
        $collection->addFieldToFilter("rma.customer_id", $customerId);
        
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
}
