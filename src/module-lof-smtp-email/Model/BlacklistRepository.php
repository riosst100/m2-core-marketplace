<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Model;

use Lof\SmtpEmail\Api\BlacklistRepositoryInterface;
use Lof\SmtpEmail\Api\Data\BlacklistInterface;
use Lof\SmtpEmail\Api\Data\BlacklistInterfaceFactory;
use Lof\SmtpEmail\Api\Data\BlacklistSearchResultsInterfaceFactory;
use Lof\SmtpEmail\Model\ResourceModel\Blacklist as ResourceBlacklist;
use Lof\SmtpEmail\Model\ResourceModel\Blacklist\CollectionFactory as BlacklistCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BlacklistRepository implements BlacklistRepositoryInterface
{

    /**
     * @var BlacklistInterfaceFactory
     */
    protected $blacklistFactory;

    /**
     * @var BlacklistCollectionFactory
     */
    protected $blacklistCollectionFactory;

    /**
     * @var ResourceBlacklist
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var Blacklist
     */
    protected $searchResultsFactory;


    /**
     * @param ResourceBlacklist $resource
     * @param BlacklistInterfaceFactory $blacklistFactory
     * @param BlacklistCollectionFactory $blacklistCollectionFactory
     * @param BlacklistSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceBlacklist $resource,
        BlacklistInterfaceFactory $blacklistFactory,
        BlacklistCollectionFactory $blacklistCollectionFactory,
        BlacklistSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->blacklistFactory = $blacklistFactory;
        $this->blacklistCollectionFactory = $blacklistCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(BlacklistInterface $blacklist)
    {
        try {
            $this->resource->save($blacklist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the blacklist: %1',
                $exception->getMessage()
            ));
        }
        return $blacklist;
    }

    /**
     * @inheritDoc
     */
    public function get($blacklistId)
    {
        $blacklist = $this->blacklistFactory->create();
        $this->resource->load($blacklist, $blacklistId);
        if (!$blacklist->getId()) {
            throw new NoSuchEntityException(__('Blacklist with id "%1" does not exist.', $blacklistId));
        }
        return $blacklist;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->blacklistCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(BlacklistInterface $blacklist)
    {
        try {
            $blacklistModel = $this->blacklistFactory->create();
            $this->resource->load($blacklistModel, $blacklist->getBlacklistId());
            $this->resource->delete($blacklistModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Blacklist: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($blacklistId)
    {
        return $this->delete($this->get($blacklistId));
    }
}

