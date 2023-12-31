<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\Quickrfq\Model;

use Lof\Quickrfq\Api\Data\QuickrfqInterface;
use Lof\Quickrfq\Api\Data\QuickrfqSearchResultsInterfaceFactory;
use Lof\Quickrfq\Api\Data\MessageInterface;
use Lof\Quickrfq\Api\Data\MessageSearchResultsInterfaceFactory;
use Lof\Quickrfq\Api\CustomerRepositoryInterface;
use Lof\Quickrfq\Model\ResourceModel\Quickrfq as ResourceQuickrfq;
use Lof\Quickrfq\Model\ResourceModel\Quickrfq\CollectionFactory as QuickrfqCollectionFactory;
use Lof\Quickrfq\Model\ResourceModel\Message as ResourceMessage;
use Lof\Quickrfq\Model\ResourceModel\Message\CollectionFactory as MessageCollectionFactory;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lof\Quickrfq\Helper\Data;

/**
 * Class CustomerRepository
 * @package Lof\Quickrfq\Model
 */
class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * @var ResourceQuickrfq
     */
    protected $resource;

    /**
     * @var ResourceMessage
     */
    protected $resourceMessage;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var QuickrfqCollectionFactory
     */
    protected $quickrfqCollectionFactory;

    /**
     * @var QuickrfqFactory
     */
    protected $quickrfqFactory;

    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var QuickrfqSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var MessageSearchResultsInterfaceFactory
     */
    protected $messageSearchResultsFactory;


    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param ResourceQuickrfq $resource
     * @param ResourceMessage $resourceMessage
     * @param QuickrfqFactory $quickrfqFactory
     * @param AttachmentFactory $attachmentFactory
     * @param MessageFactory $messageFactory
     * @param QuickrfqCollectionFactory $quickrfqCollectionFactory
     * @param QuickrfqSearchResultsInterfaceFactory $searchResultsFactory
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageSearchResultsInterfaceFactory $messageSearchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Data $data
     */
    public function __construct(
        ResourceQuickrfq $resource,
        ResourceMessage $resourceMessage,
        QuickrfqFactory $quickrfqFactory,
        AttachmentFactory $attachmentFactory,
        MessageFactory $messageFactory,
        QuickrfqCollectionFactory $quickrfqCollectionFactory,
        QuickrfqSearchResultsInterfaceFactory $searchResultsFactory,
        MessageCollectionFactory $messageCollectionFactory,
        MessageSearchResultsInterfaceFactory $messageSearchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        Data $data
    ) {
        $this->resource = $resource;
        $this->resourceMessage = $resourceMessage;
        $this->quickrfqFactory = $quickrfqFactory;
        $this->attachmentFactory = $attachmentFactory;
        $this->messageFactory = $messageFactory;
        $this->quickrfqCollectionFactory = $quickrfqCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->messageSearchResultsFactory = $messageSearchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->helper = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        $customerId,
        QuickrfqInterface $quickrfq
    ) {
        $quickrfqData = $this->extensibleDataObjectConverter->toNestedArray(
            $quickrfq,
            [],
            QuickrfqInterface::class
        );
        $quickrfqData['customer_id'] = $customerId;
        $quickrfqData['status'] = Quickrfq::STATUS_NEW;
        $quickrfqData['quantity'] = (int)$quickrfqData['quantity'] > 0?(int)$quickrfqData['quantity']:1;
        unset($quickrfqData['create_date']);
        unset($quickrfqData['update_date']);
        unset($quickrfqData['user_id']);
        unset($quickrfqData['user_name']);
        unset($quickrfqData['admin_quantity']);
        unset($quickrfqData['admin_price']);
        unset($quickrfqData['coupon_code']);

        $quickrfqData['comment'] = $this->helper->xss_clean($quickrfqData['comment']);

        $quickrfqModel = $this->quickrfqFactory->create()->setData($quickrfqData);

        try {
            $this->resource->save($quickrfqModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quickrfq: %1',
                $exception->getMessage()
            ));
        }
        return $quickrfqModel;
    }

    /**
     * {@inheritdoc}
     */
    public function get($customerId, $quickrfqId)
    {
        $quickrfq = $this->quickrfqFactory->create();
        $this->resource->load($quickrfq, $quickrfqId);
        if (!$quickrfq->getQuickrfqId() || $quickrfq->getCustomerId() != $customerId) {
            throw new NoSuchEntityException(__('Quickrfq with id "%1" does not exist.', $quickrfqId));
        }
        return $quickrfq;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->quickrfqCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            QuickrfqInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("customer_id", $customerId);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $attachmentCollection = $this->attachmentFactory->create()->getCollection()->addFieldToFilter("quickrfq_id", $model->getQuickrfqId());
            $attachment = $attachmentCollection->getFirstItem();
            $model->setAttachment($attachment);
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListMessage(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->messageCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            MessageInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("customer_id", $customerId);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $attachmentCollection = $this->attachmentFactory->create()->getCollection()->addFieldToFilter("message_id", $model->getEntityId());
            $attachment = $attachmentCollection->getFirstItem();
            $model->setAttachment($attachment);
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMessage(
        $customerId,
        MessageInterface $message
    ) {
        $messageData = $this->extensibleDataObjectConverter->toNestedArray(
            $message,
            [],
            MessageInterface::class
        );
        if (!$messageData['quickrfq_id']) {
            throw new CouldNotSaveException(__(
                'Could not save message because missing quote id'
            ));
        }
        $messageData['customer_id'] = $customerId;
        unset($messageData['attachment']);
        unset($messageData['created_at']);

        $messageModel = $this->messageFactory->create()->setData($messageData);

        try {
            $this->resourceMessage->save($messageModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the message: %1',
                $exception->getMessage()
            ));
        }
        return $quickrfqModel;
    }

}
