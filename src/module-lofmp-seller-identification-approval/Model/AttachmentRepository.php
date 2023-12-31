<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\SellerIdentificationApproval\Model;

use Lofmp\SellerIdentificationApproval\Api\AttachmentRepositoryInterface;
use Lofmp\SellerIdentificationApproval\Api\Data\AttachmentInterface;
use Lofmp\SellerIdentificationApproval\Api\Data\AttachmentInterfaceFactory;
use Lofmp\SellerIdentificationApproval\Api\Data\AttachmentSearchResultsInterfaceFactory;
use Lofmp\SellerIdentificationApproval\Model\ResourceModel\Attachment as ResourceAttachment;
use Lofmp\SellerIdentificationApproval\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class AttachmentRepository implements AttachmentRepositoryInterface
{

    /**
     * @var AttachmentCollectionFactory
     */
    protected $attachmentCollectionFactory;

    /**
     * @var ResourceAttachment
     */
    protected $resource;

    /**
     * @var Attachment
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var AttachmentInterfaceFactory
     */
    protected $attachmentFactory;


    /**
     * @param ResourceAttachment $resource
     * @param AttachmentInterfaceFactory $attachmentFactory
     * @param AttachmentCollectionFactory $attachmentCollectionFactory
     * @param AttachmentSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceAttachment $resource,
        AttachmentInterfaceFactory $attachmentFactory,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        AttachmentSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->attachmentFactory = $attachmentFactory;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(AttachmentInterface $attachment)
    {
        try {
            $this->resource->save($attachment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the attachment: %1',
                $exception->getMessage()
            ));
        }
        return $attachment;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $attachment = $this->attachmentFactory->create();
        $this->resource->load($attachment, $id);
        if (!$attachment->getId()) {
            throw new NoSuchEntityException(__('Attachment with id "%1" does not exist.', $id));
        }
        return $attachment;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->attachmentCollectionFactory->create();

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
    public function delete(AttachmentInterface $attachment)
    {
        try {
            $attachmentModel = $this->attachmentFactory->create();
            $this->resource->load($attachmentModel, $attachment->getAttachmentId());
            $this->resource->delete($attachmentModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Attachment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }
}

