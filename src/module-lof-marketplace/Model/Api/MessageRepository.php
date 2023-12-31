<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model\Api;

use Lof\MarketPlace\Api\CustomerMessageRepositoryInterface;
use Lof\MarketPlace\Api\SellerMessageRepositoryInterface;
use Lof\MarketPlace\Api\MessageRepositoryInterface;
use Lof\MarketPlace\Api\Data\MessageInterface;
use Lof\MarketPlace\Api\Data\MessageInterfaceFactory;
use Lof\MarketPlace\Api\Data\MessageDetailInterface;
use Lof\MarketPlace\Api\Data\MessageDetailInterfaceFactory;
use Lof\MarketPlace\Api\Data\MessageSearchResultsInterfaceFactory;
use Lof\MarketPlace\Api\Data\MessageDetailSearchResultsInterfaceFactory;
use Lof\MarketPlace\Model\Message;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\Sender;
use Lof\MarketPlace\Model\ResourceModel\Message as ResourceMessage;
use Lof\MarketPlace\Model\ResourceModel\MessageDetail as ResourceMessageDetail;
use Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory as MessageDetailCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Message\CollectionFactory as MessageCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class MessageRepository
 */
class MessageRepository implements MessageRepositoryInterface, CustomerMessageRepositoryInterface, SellerMessageRepositoryInterface
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
     * @var MessageInterfaceFactory
     */
    protected $messageFactory;

    /**
     * @var ResourceMessage
     */
    protected $resource;

    /**
     * @var MessageCollectionFactory
     */
    protected $messageCollectionFactory;

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
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * SellerMessageRepository constructor.
     * @param MessageDetailCollectionFactory $messageDetailCollectionFactory
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageInterfaceFactory $messageFactory
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param MessageDetailSearchResultsInterfaceFactory $detailResultsFactory
     * @param MessageDetailInterfaceFactory $messageDetailFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceMessageDetail $resourceDetail
     * @param ResourceMessage $resource
     * @param SellerFactory $sellerFactory
     * @param Data $helperData
     * @param Sender $sender
     * @param CustomerRepositoryInterface $customerRepository
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        MessageDetailCollectionFactory $messageDetailCollectionFactory,
        MessageCollectionFactory $messageCollectionFactory,
        MessageInterfaceFactory $messageFactory,
        MessageSearchResultsInterfaceFactory $searchResultsFactory,
        MessageDetailSearchResultsInterfaceFactory $detailResultsFactory,
        MessageDetailInterfaceFactory $messageDetailFactory,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ResourceMessageDetail $resourceDetail,
        ResourceMessage $resource,
        SellerFactory $sellerFactory,
        Data $helperData,
        Sender $sender,
        CustomerRepositoryInterface $customerRepository
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

        $this->messageFactory = $messageFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->sender = $sender;
        $this->customerRepository = $customerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $collection = $this->messageCollectionFactory->create();

            $this->collectionProcessor->process($searchCriteria, $collection);

            $collection->addFieldToFilter("receiver_id", $seller->getId());

            $searchResults = $this->searchAdminResultsFactory->create();
            $searchResults->setSearchCriteria($searchCriteria);
            $searchResults->setItems($collection->getItems());
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist.', $customerId));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sellerGetDetails(
        int $customerId,
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $seller = $this->getSellerByCustomer($customerId);

        if ($seller && $seller->getId()) {
            $collection = $this->getDetailsCollection($messageId, $searchCriteria);
            $collection->addFieldToFilter("receiver_id", $seller->getId());

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
     * {@inheritdoc}
     */
    public function sellerReplyMessage(int $customerId, int $messageId, string $message)
    {
        $seller = $this->getSellerByCustomer($customerId);
        $message = $this->get($messageId);

        if ($seller && $seller->getId() && $message && $message->getMessageId()) {
            /** save message Detail */
            $messageDetail = $this->messageDetailFactory->create();
            $messageDetail->setMessageAdmin(0)
                ->setMessageId($messageId)
                ->setSenderId($seller->getId())
                ->setSenderEmail($seller->getEmail())
                ->setSenderName($seller->getName())
                ->setReceiverId($message->getSenderId())
                ->setReceiverEmail($message->getSenderEmail())
                ->setReceiverName($message->getSenderEmail())
                ->setContent($message)
                ->setSellerSend(1)
                ->setIsRead(1);

            $messageDetail = $this->saveDetail($messageDetail);

            return $messageDetail;
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist or message is not exists.', $customerId));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sellerDeleteMessage(int $customerId, int $messageId)
    {
        $seller = $this->getSellerByCustomer($customerId);
        $message = $this->get($messageId);

        if ($seller && $seller->getId() && $message && $message->getMessageId()) {
            return $this->deleteById($messageId);
        } else {
            throw new NoSuchEntityException(__('Seller with customerId "%1" does not exist or message is not exists.', $customerId));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->messageCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("sender_id", $customerId);

        $searchResults = $this->searchAdminResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function getMyDetails(
        int $customerId,
        int $messageId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->getDetailsCollection($messageId, $searchCriteria);
        $collection->addFieldToFilter("sender_id", $customerId);

        $searchResults = $this->detailResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function sendMessage(int $customerId, string $sellerUrl, string $subject, string $message)
    {
        $seller = $this->getSellerByUrl($sellerUrl);
        if ($seller && $seller->getId()) {
            $customer = $this->customerRepository->getById($customerId);
            $messageModel = $this->messageFactory->create();
            $messageModel->setAdminId(0)
                ->setSenderId($customerId)
                ->setSellerEmail($customer->getEmail())
                ->setSellerName($customer->getFirstname())
                ->setSellerSend(0)
                ->setSubject($subject)
                ->setDescription($message)
                ->setReceiverId($seller->getId())
                ->setOwnerId($seller->getId())
                ->setStatus(Message::STATUS_SENT)
                ->setIsRead(1);
            return $this->save($messageModel);
        } else {
            throw new NoSuchEntityException(__('Seller with url "%1" does not exist.', $sellerUrl));
        }
    }

    /**
     * @inheritdoc
     */
    public function replyMessage(int $customerId, int $messageId, string $message)
    {
        $messageModel = $this->get($messageId);
        $sellerId = $messageModel->getReceiverId();
        $seller = $this->getSellerById($sellerId);

        if ($seller && $seller->getId()) {
            /** save message Detail */
            $customer = $this->customerRepository->getById($customerId);
            $messageDetail = $this->messageDetailFactory->create();
            $messageDetail->setMessageAdmin(0)
                ->setSenderId($customerId)
                ->setMessageId($messageId)
                ->setSenderEmail($customer->getEmail())
                ->setSenderName($customer->getFirstname())
                ->setSellerSend($seller->getId())
                ->setReceiverId($seller->getId())
                ->setReceiverEmail($seller->getEmail())
                ->setReceiverName($seller->getName())
                ->setContent($message)
                ->setIsRead(1);

            $messageDetail = $this->saveDetail($messageDetail);

            $data = $messageDetail->getData();
            $data['seller_send'] = 0;
            $data['namestore'] = $customer->getFirstname() . ' ' . $customer->getLastname();
            $data['sender_name'] = $this->helper->getStoreName();
            $data['receiver_email'] = $seller->getEmail();
            $data['urllogin'] = $this->helper->getBaseStoreUrl().'customer/account/login';
            if ($this->helper->getConfig('email_settings/enable_send_email')) {
                $this->sender->replyMessage($data);
            }
            return $messageDetail;
        } else {
            throw new NoSuchEntityException(__('Seller does not exist.'));
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteMessage(int $customerId, int $messageId)
    {
        $message = $this->get($messageId);

        if ($customerId && $message && $message->getMessageId() && ($message->getOwnerId() == $customerId)) {
            return $this->deleteById($messageId);
        } else {
            throw new NoSuchEntityException(__('Message Id %1 is not exists.', $messageId));
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
        $message = $this->get($messageId);
        if ($message && $message->getMessageId() && $message->getSenderId() == $customerId) {
            /** save message */
            $availableStatus = [
                "sent" => Message::STATUS_SENT,
                "draft" => Message::STATUS_DRAFT,
                "read" => Message::STATUS_READ,
                "unread" => Message::STATUS_UNREAD
            ];
            $status = in_array($status, $availableStatus) ? $status : "";
            $message->setIsRead(1);

            if (isset($availableStatus[$status])) {
                $message->setStatus((int)$availableStatus[$status]);
            }
            return $this->save($message);
        } else {
            throw new NoSuchEntityException(__('Not found any messages for customer "%1".', $customerId));
        }
    }

    /**
     * @inheritDoc
     */
    public function get($messageId)
    {
        $message = $this->messageFactory->create();
        $this->resource->load($message, $messageId);
        if (!$message->getMessageId()) {
            throw new NoSuchEntityException(__('Message with id "%1" does not exist.', $messageId));
        }
        return $message;
    }

    /**
     * @inheritdoc
     */
    public function save(MessageInterface $message)
    {
        try {
            $message->setCreatedAt(null);

            if (!$message->getMessageId() && (!$message->getDescription() || !$message->getSubject() || !$message->getSenderId())) {
                throw new CouldNotSaveException(__(
                    'Could not save the message: missing description, senderId or subject.'
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
    public function delete(MessageInterface $message)
    {
        try {
            $collection = $this->messageDetailCollectionFactory->create();
            $collection->addFieldToFilter("message_id", $message->getMessageId);
            $collection->addFieldToFilter("message_admin", 0);

            /** @var \Lof\MarketPlace\Model\Message $messageModel */
            $messageModel = $this->messageFactory->create();
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
        $collection = $this->messageDetailCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("message_id", $messageId);
        $collection->addFieldToFilter("message_admin", 0);

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

    /**
     * get seller by seller id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerById(int $sellerId)
    {
        if (!isset($this->_sellers["seller-".$sellerId])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter("seller_id", $sellerId)
                    ->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers["seller-".$sellerId] = $seller;
        }
        return $this->_sellers["seller-".$sellerId];
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl(string $sellerUrl)
    {
        if (!isset($this->_sellers[$sellerUrl])) {
            $seller = $this->sellerFactory->create()->getCollection()
                    ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                    ->addFieldToFilter("status", \Lof\MarketPlace\Model\Seller::STATUS_ENABLED)
                    ->getFirstItem();
            $this->_sellers[$sellerUrl] = $seller;
        }
        return $this->_sellers[$sellerUrl];
    }
}
