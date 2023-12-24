<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Model;

use Lof\SmtpEmail\Api\Data\EmaillogInterface;
use Lof\SmtpEmail\Api\Data\EmaillogInterfaceFactory;
use Lof\SmtpEmail\Api\Data\EmaillogSearchResultsInterfaceFactory;
use Lof\SmtpEmail\Api\EmaillogRepositoryInterface;
use Lof\SmtpEmail\Model\ResourceModel\Emaillog as ResourceEmaillog;
use Lof\SmtpEmail\Model\ResourceModel\Emaillog\CollectionFactory as EmaillogCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class EmaillogRepository implements EmaillogRepositoryInterface
{

    /**
     * @var Emaillog
     */
    protected $searchResultsFactory;

    /**
     * @var EmaillogInterfaceFactory
     */
    protected $emaillogFactory;

    /**
     * @var ResourceEmaillog
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var EmaillogCollectionFactory
     */
    protected $emaillogCollectionFactory;


    /**
     * @param ResourceEmaillog $resource
     * @param EmaillogInterfaceFactory $emaillogFactory
     * @param EmaillogCollectionFactory $emaillogCollectionFactory
     * @param EmaillogSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceEmaillog $resource,
        EmaillogInterfaceFactory $emaillogFactory,
        EmaillogCollectionFactory $emaillogCollectionFactory,
        EmaillogSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->emaillogFactory = $emaillogFactory;
        $this->emaillogCollectionFactory = $emaillogCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(EmaillogInterface $emaillog)
    {
        try {
            $this->resource->save($emaillog);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the emaillog: %1',
                $exception->getMessage()
            ));
        }
        return $emaillog;
    }

    /**
     * @inheritDoc
     */
    public function get($emaillogId)
    {
        $emaillog = $this->emaillogFactory->create();
        $this->resource->load($emaillog, $emaillogId);
        if (!$emaillog->getId()) {
            throw new NoSuchEntityException(__('Emaillog with id "%1" does not exist.', $emaillogId));
        }
        return $emaillog;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->emaillogCollectionFactory->create();
        
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
    public function delete(EmaillogInterface $emaillog)
    {
        try {
            $emaillogModel = $this->emaillogFactory->create();
            $this->resource->load($emaillogModel, $emaillog->getEmaillogId());
            $this->resource->delete($emaillogModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Emaillog: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($emaillogId)
    {
        return $this->delete($this->get($emaillogId));
    }
}

