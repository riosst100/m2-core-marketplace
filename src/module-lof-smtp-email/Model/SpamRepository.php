<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Model;

use Lof\SmtpEmail\Api\Data\SpamInterface;
use Lof\SmtpEmail\Api\Data\SpamInterfaceFactory;
use Lof\SmtpEmail\Api\Data\SpamSearchResultsInterfaceFactory;
use Lof\SmtpEmail\Api\SpamRepositoryInterface;
use Lof\SmtpEmail\Model\ResourceModel\Spam as ResourceSpam;
use Lof\SmtpEmail\Model\ResourceModel\Spam\CollectionFactory as SpamCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class SpamRepository implements SpamRepositoryInterface
{

    /**
     * @var Spam
     */
    protected $searchResultsFactory;

    /**
     * @var ResourceSpam
     */
    protected $resource;

    /**
     * @var SpamInterfaceFactory
     */
    protected $spamFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SpamCollectionFactory
     */
    protected $spamCollectionFactory;


    /**
     * @param ResourceSpam $resource
     * @param SpamInterfaceFactory $spamFactory
     * @param SpamCollectionFactory $spamCollectionFactory
     * @param SpamSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceSpam $resource,
        SpamInterfaceFactory $spamFactory,
        SpamCollectionFactory $spamCollectionFactory,
        SpamSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->spamFactory = $spamFactory;
        $this->spamCollectionFactory = $spamCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(SpamInterface $spam)
    {
        try {
            $this->resource->save($spam);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the spam: %1',
                $exception->getMessage()
            ));
        }
        return $spam;
    }

    /**
     * @inheritDoc
     */
    public function get($spamId)
    {
        $spam = $this->spamFactory->create();
        $this->resource->load($spam, $spamId);
        if (!$spam->getId()) {
            throw new NoSuchEntityException(__('Spam with id "%1" does not exist.', $spamId));
        }
        return $spam;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->spamCollectionFactory->create();
        
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
    public function delete(SpamInterface $spam)
    {
        try {
            $spamModel = $this->spamFactory->create();
            $this->resource->load($spamModel, $spam->getSpamId());
            $this->resource->delete($spamModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Spam: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($spamId)
    {
        return $this->delete($this->get($spamId));
    }
}

