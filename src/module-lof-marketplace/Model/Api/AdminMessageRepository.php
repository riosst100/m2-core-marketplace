<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model\Api;

use Lof\MarketPlace\Api\AdminMessageRepositoryInterface;
use Lof\MarketPlace\Api\Data\AdminMessageInterface;
use Lof\MarketPlace\Api\Data\AdminMessageInterfaceFactory;
use Lof\MarketPlace\Api\Data\MessageDetailInterface;
use Lof\MarketPlace\Api\Data\MessageDetailInterfaceFactory;
use Lof\MarketPlace\Api\Data\AdminMessageSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\Data\MessageDetailSearchResultsInterfaceFactory;
use Lof\MarketPlace\Model\MessageAdmin;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\Sender;
use Lof\MarketPlace\Model\ResourceModel\MessageAdmin as ResourceMessageAdmin;
use Lof\MarketPlace\Model\ResourceModel\MessageDetail as ResourceMessageDetail;
use Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory as MessageDetailCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\MessageAdmin\CollectionFactory as MessageAdminCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class AdminMessageRepository
 */
class AdminMessageRepository implements AdminMessageRepositoryInterface
{

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

     /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var AdminMessageInterfaceFactory
     */
    protected $adminMessageFactory;

    /**
     * @var ResourceMessageAdmin
     */
    protected $resource;

    /**
     * @var MessageAdminCollectionFactory
     */
    protected $messageAdminCollectionFactory;

    /**
     * @var MessageDetailCollectionFactory
     */
    protected $messageDetailCollectionFactory;

    /**
     * @var MessageDetailSearchResultsInterfaceFactory
     */
    protected $detailResultsFactory;

    /**
     * @var MessageDetailInterfaceFactory
     */
    protected $messageDetailFactory;

    /**
     * @var ResourceMessageDetail
     */
    protected $resourceDetail;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * SellerMessageRepository constructor.
     * @param MessageDetailCollectionFactory $messageDetailCollectionFactory
     * @param MessageAdminCollectionFactory $messageAdminCollectionFactory
     * @param AdminMessageInterfaceFactory $adminMessageFactory
     * @param AdminMessageSearchResultsInterfaceFactory $searchAdminResultsFactory
     * @param MessageDetailSearchResultsInterfaceFactory $detailResultsFactory
     * @param MessageDetailInterfaceFactory $messageDetailFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceMessageDetail $resourceDetail
     * @param ResourceMessageAdmin $resource
     * @param SellerFactory $sellerFactory
     * @param Data $helperData
     * @param Sender $sender
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        MessageDetailCollectionFactory $messageDetailCollectionFactory,
        MessageAdminCollectionFactory $messageAdminCollectionFactory,
        AdminMessageInterfaceFactory $adminMessageFactory,
        AdminMessageSearchResultsInterfaceFactory $searchAdminResultsFactory,
        MessageDetailSearchResultsInterfaceFactory $detailResultsFactory,
        MessageDetailInterfaceFactory $messageDetailFactory,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ResourceMessageDetail $resourceDetail,
        ResourceMessageAdmin $resource,
        SellerFactory $sellerFactory,
        Data $helperData,
        Sender $sender
    ) {
        $this->messageDetailCollectionFactory = $messageDetailCollectionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->sellerFactory = $sellerFactory;
        $this->helperData = $helperData;
        $this->collectionProcessor = $collectionProcessor;
        $this->resource = $resource;
        $this->detailResultsFactory = $detailResultsFactory;
        $this->messageDetailFactory = $messageDetailFactory;
        $this->resourceDetail = $resourceDetail;

        $this->adminMessageFactory = $adminMessageFactory;
        $this->searchAdminResultsFactory = $searchAdminResultsFactory;
        $this->messageAdminCollectionFactory = $messageAdminCollectionFactory;
        $this->sender = $sender;
    }

    /**
     * {@inheritdoc}
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $collection = $this->messageAdminCollectionFactory->create();

            $this->collectionProcessor->process($criteria, $collection);

            $collection->addFieldToFilter("seller_id", $seller->getId());

            $searchResults = $this->searchAdminResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);
            $searchResults->setItems($collection->getItems());
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritDoc
     */
    public function getMyMessage(int $customerId, int $messageId)
    {
        $seller = $this->getSellerByCustomer($customerId);
        if ($seller && $seller->getId()) {
            $message = $this->get($messageId);
            if ($seller->getId() != $message->getSellerId()) {
                throw new NoSuchEntityException(__('Message with ID "%1" is not exists.', $messageId));
            }
            return $message;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritDoc
     */
    public function getMyDetails(
        int $customerId,
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $seller = $this->getSellerByCustomer($customerId);
        if ($seller && $seller->getId()) {
            $collection = $this->getDetailsCollection($messageId, $searchCriteria);
            $collection->addFieldToFilter("seller_id", $seller->getId());

            $searchResults = $this->detailResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);
            $searchResults->setItems($collection->getItems());
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritdoc
     */
    public function sendMessage(int $customerId, string $subject, string $message)
    {
        $seller = $this->getSellerByCustomer($customerId);
        if ($seller && $seller->getId()) {
            $messageModel = $this->adminMessageFactory->create();
            $messageModel->setAdminId(0)
                ->setSellerId($seller->getId())
                ->setSellerEmail($seller->getEmail())
                ->setSellerName($seller->getName())
                ->setSellerSend(1)
                ->setSubject($subject)
                ->setDescription($message)
                ->setReceiverId($seller->getId())
                ->setStatus(MessageAdmin::STATUS_SENT)
                ->setIsRead(1);
            $messageModel = $this->save($messageModel);

            /** @var \Lof\MarketPlace\Api\Data\MessageDetailInterface $messageDetail */
            $messageDetail = $this->messageDetailFactory->create();
            $messageDetail->setMessageAdmin(1)
                ->getSellerSend($seller->getId())
                ->setMessageId($messageModel->getMessageId())
                ->setSenderEmail($seller->getEmail())
                ->setSenderName($seller->getName())
                ->setSellerSend(1)
                ->setContent($message)
                ->setIsRead(0);
            $messageDetail = $this->saveDetail($messageDetail);

            $data = $messageModel->getData();
            $data['namestore'] = $this->helperData->getStoreName();
            $data['urllogin'] = $this->helperData->getBaseStoreUrl().'customer/account/login';
            if ($this->helperData->getConfig('email_settings/enable_send_email')) {
                $this->sender->sellerNewMessage($data);
            }

            return $messageModel;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritdoc
     */
    public function replyMessage(int $customerId, int $messageId, string $message)
    {
        $seller = $this->getSellerByCustomer($customerId);
        $messageModel = $this->get($messageId);
        if ($seller && $seller->getId() && $messageModel->getMessageId()) {
            /** save message Detail */
            $messageDetail = $this->messageDetailFactory->create();
            $messageDetail->setMessageAdmin(1)
                ->setSenderId($seller->getId())
                ->setMessageId($messageId)
                ->setSenderEmail($seller->getEmail())
                ->setSenderName($seller->getName())
                ->setSellerSend(1)
                ->setContent($message)
                ->setIsRead(0);
            $messageDetail = $this->saveDetail($messageDetail);

            return $messageDetail;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritdoc
     */
    public function setIsRead(
        int $customerId,
        int $messageId,
        string $status = null
    ) {
        $seller = $this->getSellerByCustomer($customerId);
        if ($seller && $seller->getId()) {
            /** save message */
            $availableStatus = [
                "sent" => MessageAdmin::STATUS_SENT,
                "draft" => MessageAdmin::STATUS_DRAFT,
                "read" => MessageAdmin::STATUS_READ,
                "unread" => MessageAdmin::STATUS_UNREAD
            ];
            $status = in_array($status, $availableStatus) ? $status : "";
            $message = $this->get($messageId);
            $message->setIsRead(1);

            if (isset($availableStatus[$status])) {
                $message->setStatus((int)$availableStatus[$status]);
            }
            return $this->save($message);
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * @inheritdoc
     */
    public function save(AdminMessageInterface $message)
    {
        try {
            $message->setCreatedAt(null);

            if (!$message->getMessageId() && (!$message->getDescription() || !$message->getSubject() || !$message->getSellerId())) {
                throw new CouldNotSaveException(__(
                    'Could not save the message: missing description, sellerID or subject.'
                ));
            }
            $this->resource->save($message);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the message: %1',
                $exception->getMessage()
            ));
        }
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function saveDetail(MessageDetailInterface $messageDetail)
    {
        try {
            $messageDetail->setCreatedAt(null);

            if (!$messageDetail->getDetailId() && (!$messageDetail->getMessageId() || !$messageDetail->getSenderEmail() || !$messageDetail->getContent())) {
                throw new CouldNotSaveException(__(
                    'Could not save the message: missing content, messageId or sender email.'
                ));
            }
            $this->resourceDetail->save($messageDetail);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the message detail: %1',
                $exception->getMessage()
            ));
        }
        return $messageDetail;
    }

    /**
     * @inheritDoc
     */
    public function get($messageId)
    {
        $message = $this->adminMessageFactory->create();
        $this->resource->load($message, $messageId);
        if (!$message->getEntityId()) {
            throw new NoSuchEntityException(__('Admin Message with id "%1" does not exist.', $messageId));
        }
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->messageAdminCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function getDetails(
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->getDetailsCollection($messageId, $searchCriteria);
        $searchResults = $this->detailResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(AdminMessageInterface $message)
    {
        try {
            $collection = $this->messageAdminCollectionFactory->create();
            $collection->addFieldToFilter("message_id", $message->getMessageId);
            $collection->addFieldToFilter("message_admin", 1);

            $messageModel = $this->adminMessageFactory->create();
            $this->resource->load($messageModel, $message->getMessageId());
            $this->resource->delete($messageModel);

            /** delete message details */
            foreach ($collection as $_detail) {
                $this->resourceDetail->delete($_detail);
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the admin message: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($messageId)
    {
        return $this->delete($this->get($messageId));
    }

    /**
     * get details collection
     *
     * @param int $messageId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return mixed
     */
    public function getDetailsCollection($messageId, \Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->messageAdminCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("message_id", $messageId);
        $collection->addFieldToFilter("message_admin", 1);

        return $collection;
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomer(int $customerId)
    {
        if (!isset($this->_sellers[$customerId])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter("customer_id", $customerId)
                    ->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers[$customerId] = $seller;
        }
        return $this->_sellers[$customerId];
    }
}
