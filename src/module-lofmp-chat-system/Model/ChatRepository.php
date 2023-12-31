<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Model;

use Lofmp\ChatSystem\Api\ChatMessageRepositoryInterface;
use Lof\MarketPlace\Model\Seller;
use Lofmp\ChatSystem\Api\ChatRepositoryInterface;
use Lofmp\ChatSystem\Api\Data\SubmitChatInterface;
use Lofmp\ChatSystem\Api\Data\ChatInterface;
use Lofmp\ChatSystem\Api\Data\ChatInterfaceFactory;
use Lofmp\ChatSystem\Api\Data\ChatSearchResultsInterfaceFactory;
use Lofmp\ChatSystem\Api\Data\ChatMessageInterfaceFactory;
use Lofmp\ChatSystem\Helper\Data;
use Lofmp\ChatSystem\Model\ResourceModel\Chat as ResourceChat;
use Lofmp\ChatSystem\Model\ResourceModel\Chat\CollectionFactory as ChatCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface ;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class ChatRepository implements ChatRepositoryInterface
{

    /**
     * @var ResourceChat
     */
    protected $resource;

    /**
     * @var Chat
     */
    protected $searchResultsFactory;

    /**
     * @var ChatInterfaceFactory
     */
    protected $chatFactory;

    /**
     * @var ChatCollectionFactory
     */
    protected $chatCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var ChatMessageRepositoryInterface
     */
    protected $chatMessageRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var ChatMessageInterfaceFactory
     */
    protected $chatMessageFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var array
     */
    protected $_sellers = [];

    /**
     * @param ResourceChat $resource
     * @param ChatInterfaceFactory $chatFactory
     * @param ChatCollectionFactory $chatCollectionFactory
     * @param ChatSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param ChatMessageRepositoryInterface $chatMessageRepository
     * @param CustomerRepositoryInterface $customerRepository
     * @param ChatMessageInterfaceFactory $chatMessageFactory
     * @param Data $helperData
     */
    public function __construct(
        ResourceChat $resource,
        ChatInterfaceFactory $chatFactory,
        ChatCollectionFactory $chatCollectionFactory,
        ChatSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SellerCollectionFactory $sellerCollectionFactory,
        ChatMessageRepositoryInterface $chatMessageRepository,
        CustomerRepositoryInterface $customerRepository,
        ChatMessageInterfaceFactory $chatMessageFactory,
        Data $helperData
    ) {
        $this->resource = $resource;
        $this->chatFactory = $chatFactory;
        $this->chatCollectionFactory = $chatCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->chatMessageRepository = $chatMessageRepository;
        $this->customerRepository = $customerRepository;
        $this->chatMessageFactory = $chatMessageFactory;
        $this->helperData = $helperData;
    }

    /**
     * @inheritDoc
     */
    public function save(ChatInterface $chat)
    {
        try {
            $this->resource->save($chat);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the chat: %1',
                $exception->getMessage()
            ));
        }
        return $chat;
    }

    /**
     * @inheritDoc
     */
    public function getById($chatId)
    {
        $chat = $this->chatFactory->create();
        $this->resource->load($chat, $chatId);
        if (!$chat->getChatId()) {
            throw new NoSuchEntityException(__('Chat with id "%1" does not exist.', $chatId));
        }
        return $chat;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->chatCollectionFactory->create();
        
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
    public function delete(ChatInterface $chat)
    {
        try {
            $chatModel = $this->chatFactory->create();
            $this->resource->load($chatModel, $chat->getChatId());
            $this->resource->delete($chatModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Chat: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($chatId)
    {
        return $this->delete($this->getById($chatId));
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        try{
            $collection = $this->chatCollectionFactory->create();
            foreach ($collection as $key => $model) {
                $model->delete();
            }
        }catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not detete the chat: %1',
                $exception->getMessage()
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $seller = $this->getSellerByCustomerId($customerId);

        if ($seller & $seller->getId()) {
            $collection = $this->chatCollectionFactory->create();

            $this->collectionProcessor->process($criteria, $collection);

            $collection->addFieldToFilter("seller_id", $seller->getId());

            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setSearchCriteria($criteria);

            $items = [];
            foreach ($collection as $model) {
                $items[] = $model;
            }

            $searchResults->setItems($items);
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function sellerGetChat($customerId, $chatId)
    {
        $seller = $this->getSellerByCustomerId($customerId);

        if ($seller && $seller->getId()) {
            $chat = $this->getById($chatId);
            if ($chat->getSellerId() != $seller->getId()) {
                throw new NoSuchEntityException(__('Chat with id "%1" does not exist.', $chatId));
            }
            return $chat;
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function sellerSendMessage(int $customerId, int $chatId, SubmitChatInterface $chat)
    {
        if (!isset($data["message"]) || !empty($data["message"])) {
            throw new CouldNotSaveException(__(
                'Message Content is required, please send chat message.'
            ));
        }
        $seller = $this->getSellerByCustomerId($customerId);

        if ($seller & $seller->getId()) {
            $chatModel = $this->getById($chatId);
            if (!$chatModel->getId()) {
                throw new CouldNotSaveException(__(
                    'Chat is not exists.'
                ));
            }
            $data = $chat->getData();
            $data["is_read"] = 1;
            $data["number_message"] = (int)$chatModel->getNumberMessage() + 1;
            $data["chat_id"] = $chatModel->getId();
            $data["seller_id"] = $seller->getId();
            $data["body_msg"] = $data["message"];
            $data = $this->helperData->xss_clean_array($data);
            if (isset($data["chat_id"])) {
                unset($data["chat_id"]);
            }

            try {
                $chatModel->setData($data);
                $this->resource->save($chatModel);

                $data["user_id"] = 0;
                $data["answered"] = 1;

                $chatMessage = $this->chatMessageFactory->create();
                $chatMessage->setData($data);
                $this->chatMessageRepository->save($chatMessage);

                return $chatModel;

            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the chat: %1',
                    $exception->getMessage()
                ));
            }
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function sellerDeleteById(int $customerId, $chatId)
    {
        return $this->delete($this->sellerGetChat($customerId, $chatId));
    }

    /**
     * @inheritDoc
     */
    public function sellerClear(int $customerId)
    {
        try {
            $collection = $this->chatCollectionFactory->create();
            $collection->addFieldToFilter("customer_id", $customerId);
            foreach ($collection as $key => $model) {
                $model->delete();
            }
            return true;
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not delete the chat: %1',
                $exception->getMessage()
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function customerSendMessage(int $customerId, string $sellerUrl, SubmitChatInterface $chat)
    {
        if (!isset($data["message"]) || !empty($data["message"])) {
            throw new CouldNotSaveException(__(
                'Message Content is required, please send chat message.'
            ));
        }
        $seller = $this->getSellerByUrl($sellerUrl);

        if ($seller & $seller->getId()) {
            /** verify data chat */
            $customer = $this->customerRepository->getById($customerId);
            $foundChat = $this->chatCollectionFactory->create()
                        ->addFieldToFilter("customer_id", $customerId)
                        ->addFieldToFilter("customer_email", $customer->getEmail())
                        ->getFirstItem();

            $data = $chat->getData();
            $data["is_read"] = 1;
            $data["number_message"] = 1;
            $data["customer_id"] = $customerId;
            $data["customer_email"] = $customer->getEmail();
            $data["customer_name"] = $customer->getFirstname(). " ".$customer->getLastname();
            $data["seller_id"] = $seller->getId();
            $data["body_msg"] = $data["message"];
            $chatId = 0;
            if ($foundChat && $foundChat->getId()) {
                $chatId= $foundChat->getId();
            }
            $data = $this->helperData->xss_clean_array($data);
            if (isset($data["chat_id"])) {
                unset($data["chat_id"]);
            }

            try {
                if (!$chatId) {
                    $chatModel = $this->chatFactory->create();
                } else {
                    $chatModel = $this->getById((int)$chatId);
                    $data["number_message"] = (int)$chatModel->getNumberMessage() + 1;
                }
                $chatModel->setData($data);
                $this->resource->save($chatModel);

                $data["chat_id"] = $chatModel->getId();
                $data["user_id"] = 0;
                $data["answered"] = 1;

                $chatMessage = $this->chatMessageFactory->create();
                $chatMessage->setData($data);
                $this->chatMessageRepository->save($chatMessage);

                return $chatModel;
            } catch (\Exception $exception) {
                throw new CouldNotSaveException(__(
                    'Could not save the chat: %1',
                    $exception->getMessage()
                ));
            }
        } else {
            throw new CouldNotSaveException(__(
                'Could chat for seller url: %1',
                $sellerUrl
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function customerChatList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->chatCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("customer_id", $customerId);

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
    public function customerChatById(int $customerId, $chatId)
    {
        $chat = $this->getById($chatId);
        if ($chat->getCustomerId() != $customerId) {
            throw new NoSuchEntityException(__('Chat with id "%1" does not exist.', $chatId));
        }
        return $chat;
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return Seller
     */
    protected function getSellerByUrl(string $sellerUrl)
    {
        if (!isset($this->_sellers[$sellerUrl])) {
            $seller = $this->sellerCollectionFactory->create()
                ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
                ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                ->getFirstItem();
            $this->_sellers[$sellerUrl] = $seller;
        }
        return $this->_sellers[$sellerUrl];
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomerId(int $customerId)
    {
        if (!isset($this->_sellers[$customerId])) {
            $sellerCollection = $this->sellerCollectionFactory->create();
            $this->_sellers[$customerId] = $sellerCollection
                ->addFieldToFilter("customer_id", $customerId)
                ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                ->getFirstItem();
        }
        return $this->_sellers[$customerId];
    }
}

