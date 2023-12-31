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

use Lofmp\Rma\Api\Repository\CustomerMessageRepositoryInterface;
use Lofmp\Rma\Api\Data\MessageSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\MessageInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Message as ResourceMessage;
use Lofmp\Rma\Model\ResourceModel\Message\CollectionFactory as MessageCollectionFactory;
use Lofmp\Rma\Model\RmaFactory as RmaModelFactory;
use Magento\Store\Model\StoreManagerInterface;

class CustomerMessageRepository implements CustomerMessageRepositoryInterface
{
    /**
     * @var ResourceMessage
     */
    protected $resource;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var MessageCollectionFactory
     */
    protected $messageCollectionFactory;

    /**
     * @var MessageSearchResultsInterfaceFactory
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
     * @var MessageInterfaceFactory
     */
    protected $dataMessageFactory;

    /**
     * @var RmaModelFactory
     */
    protected $rmaModelFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    

    /**
     * @param ResourceMessage $resource
     * @param MessageFactory $messageFactory
     * @param MessageInterfaceFactory $dataMessageFactory
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param RmaModelFactory $rmaModelFactory
     */
    public function __construct(
        ResourceMessage $resource,
        MessageFactory $messageFactory,
        MessageInterfaceFactory $dataMessageFactory,
        MessageCollectionFactory $messageCollectionFactory,
        MessageSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        RmaModelFactory $rmaModelFactory
    ) {
        $this->resource = $resource;
        $this->messageFactory = $messageFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMessageFactory = $dataMessageFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->rmaModelFactory = $rmaModelFactory;
    }

    public function create()
    {
        return $this->messageFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        $customerId,
        \Lofmp\Rma\Api\Data\MessageInterface $message
    ) {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        try {
            $messageCustomerId = $message->getCustomerId();
            if($customerId == $messageCustomerId){
                $message->getResource()->save($message);
            }else {
                throw new CouldNotSaveException(__(
                    'Could not save the message because wrong Customer ID'
                ));
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the message: %1',
                $exception->getMessage()
            ));
        }
        return $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($customerId, $messageId)
    {
        if(!$customerId){
            throw new NoSuchEntityException(__('You should login with your account.'));
        }
        $message = $this->messageFactory->create();
        $message->getResource()->load($message, $messageId);
        if (!$message->getId()) {
            throw new NoSuchEntityException(__('message with id "%1" does not exist.', $messageId));
        }
        $messageCustomerId = $message->getCustomerId();
        if($customerId != $messageCustomerId){
            throw new NoSuchEntityException(__('rma message with id "%1" does not exist for this Customer.', $messageId));
        }
        return $message;
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
        $collection = $this->messageCollectionFactory->create();
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
        //Add filter for this customer ID
        $collection->addFieldToFilter("customer_id", $customerId);
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
    public function getListByRma(
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
            $collection = $this->messageCollectionFactory->create();
            $collection->joinRmaTable();
            $rma_ids = is_array($rma_ids)?$rma_ids:[];
            $rma_ids[] = $rma->getId();

            $collection->addFieldToFilter("main_table.rma_id", ["in" => $rma_ids]);
            $collection->addFieldToFilter("main_table.customer_id", $customerId);

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
                'Could not load messages because RMA is not exists.'
            ));
        }
    }
}
