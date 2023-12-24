<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Model;

use Lof\SmtpEmail\Api\BlockipRepositoryInterface;
use Lof\SmtpEmail\Api\Data\BlockipInterface;
use Lof\SmtpEmail\Api\Data\BlockipInterfaceFactory;
use Lof\SmtpEmail\Api\Data\BlockipSearchResultsInterfaceFactory;
use Lof\SmtpEmail\Model\ResourceModel\Blockip as ResourceBlockip;
use Lof\SmtpEmail\Model\ResourceModel\Blockip\CollectionFactory as BlockipCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BlockipRepository implements BlockipRepositoryInterface
{

    /**
     * @var ResourceBlockip
     */
    protected $resource;

    /**
     * @var BlockipInterfaceFactory
     */
    protected $blockipFactory;

    /**
     * @var Blockip
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var BlockipCollectionFactory
     */
    protected $blockipCollectionFactory;


    /**
     * @param ResourceBlockip $resource
     * @param BlockipInterfaceFactory $blockipFactory
     * @param BlockipCollectionFactory $blockipCollectionFactory
     * @param BlockipSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceBlockip $resource,
        BlockipInterfaceFactory $blockipFactory,
        BlockipCollectionFactory $blockipCollectionFactory,
        BlockipSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->blockipFactory = $blockipFactory;
        $this->blockipCollectionFactory = $blockipCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(BlockipInterface $blockip)
    {
        try {
            $this->resource->save($blockip);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the blockip: %1',
                $exception->getMessage()
            ));
        }
        return $blockip;
    }

    /**
     * @inheritDoc
     */
    public function get($blockipId)
    {
        $blockip = $this->blockipFactory->create();
        $this->resource->load($blockip, $blockipId);
        if (!$blockip->getId()) {
            throw new NoSuchEntityException(__('Blockip with id "%1" does not exist.', $blockipId));
        }
        return $blockip;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->blockipCollectionFactory->create();
        
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
    public function delete(BlockipInterface $blockip)
    {
        try {
            $blockipModel = $this->blockipFactory->create();
            $this->resource->load($blockipModel, $blockip->getBlockipId());
            $this->resource->delete($blockipModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Blockip: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($blockipId)
    {
        return $this->delete($this->get($blockipId));
    }
}

