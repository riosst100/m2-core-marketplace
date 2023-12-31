<?php


namespace Lofmp\Rma\Model;

use Lofmp\Rma\Api\Repository\AttachmentRepositoryInterface;
use Lofmp\Rma\Api\Data\AttachmentSearchResultsInterfaceFactory;
use Lofmp\Rma\Api\Data\AttachmentInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lofmp\Rma\Model\ResourceModel\Attachment as ResourceAttachment;
use Lofmp\Rma\Model\ResourceModel\Attachment\CollectionFactory as AttachmentCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class AttachmentRepository implements attachmentRepositoryInterface
{

    protected $resource;

    protected $attachmentFactory;

    protected $attachmentCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataAttachmentFactory;

    private $storeManager;


    /**
     * @param ResourceAttachment $resource
     * @param AttachmentFactory $attachmentFactory
     * @param AttachmentInterfaceFactory $dataAttachmentFactory
     * @param AttachmentCollectionFactory $attachmentCollectionFactory
     * @param AttachmentSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceAttachment $resource,
        AttachmentFactory $attachmentFactory,
        AttachmentInterfaceFactory $dataAttachmentFactory,
        AttachmentCollectionFactory $attachmentCollectionFactory,
        AttachmentSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->attachmentFactory = $attachmentFactory;
        $this->attachmentCollectionFactory = $attachmentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataAttachmentFactory = $dataAttachmentFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    public function create()
    {
        return $this->attachmentFactory->create();
    }


    /**
     * {@inheritdoc}
     */
    public function save(
        \Lofmp\Rma\Api\Data\AttachmentInterface $attachment
    ) {
        /* if (empty($attachment->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $attachment->setStoreId($storeId);
        } */
        try {
            $attachment->getResource()->save($attachment);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the attachment: %1',
                $exception->getMessage()
            ));
        }
        return $attachment;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($attachmentId)
    {
        $attachment = $this->attachmentFactory->create();
        $attachment->getResource()->load($attachment, $attachmentId);
        if (!$attachment->getId()) {
            throw new NoSuchEntityException(__('Attachment with id "%1" does not exist.', $attachmentId));
        }
        return $attachment;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->attachmentCollectionFactory->create();
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
        \Lofmp\Rma\Api\Data\AttachmentInterface $attachment
    ) {
        try {
            $attachment->getResource()->delete($attachment);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Attachment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($attachmentId)
    {
        return $this->delete($this->getById($attachmentId));
    }
}
