<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lofmp\ChatSystem\Model;

use Lof\MarketPlace\Model\Seller;
use Lofmp\ChatSystem\Api\ChatMessageRepositoryInterface;
use Lofmp\ChatSystem\Api\Data\ChatMessageInterface;
use Lofmp\ChatSystem\Api\Data\ChatMessageInterfaceFactory;
use Lofmp\ChatSystem\Api\Data\ChatMessageSearchResultsInterfaceFactory;
use Lofmp\ChatSystem\Model\ResourceModel\ChatMessage as ResourceChatMessage;
use Lofmp\ChatSystem\Model\ResourceModel\ChatMessage\CollectionFactory as ChatMessageCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class ChatMessageRepository implements ChatMessageRepositoryInterface
{

    /**
     * @var ResourceChatMessage
     */
    protected $resource;

    /**
     * @var ChatMessageInterfaceFactory
     */
    protected $chatMessageFactory;

    /**
     * @var ChatMessage
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var ChatMessageCollectionFactory
     */
    protected $chatMessageCollectionFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    protected $customerRepository;
    protected $_helper;

    /**
     * @var array
     */
    protected $_sellers = [];

    /**
     * @param ResourceChatMessage $resource
     * @param ChatMessageInterfaceFactory $chatMessageFactory
     * @param ChatMessageCollectionFactory $chatMessageCollectionFactory
     * @param ChatMessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param \Lofmp\ChatSystem\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param SellerCollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        ResourceChatMessage $resource,
        ChatMessageInterfaceFactory $chatMessageFactory,
        ChatMessageCollectionFactory $chatMessageCollectionFactory,
        ChatMessageSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        \Lofmp\ChatSystem\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        SellerCollectionFactory $sellerCollectionFactory
    ) {
        $this->resource = $resource;
        $this->chatMessageFactory = $chatMessageFactory;
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->_helper = $helper;
        $this->customerRepository = $customerRepository;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(ChatMessageInterface $chatMessage)
    {
        try {
            $this->resource->save($chatMessage);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the chat message: %1',
                $exception->getMessage()
            ));
        }
        return $chatMessage;
    }

    /**
     * @inheritDoc
     */
    public function sellerGetChatMessage(int $customerId, $messageId)
    {
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller & $seller->getId()) {
            $chatMessage = $this->chatMessageFactory->create();
            $this->resource->load($chatMessage, $messageId);
            if (!$chatMessage->getMessageId()) {
                throw new NoSuchEntityException(__('ChatMessage with id "%1" does not exist.', $messageId));
            }
            return $chatMessage;
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * @param int $customerId
     * @param int $chatId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function sellerGetListByChatId(
        int $customerId,
        int $chatId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $seller = $this->getSellerByCustomerId($customerId);
        $collection = $this->chatMessageCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $collection->addFieldToFilter('chat_id', $chatId);
        $collection->addFieldToFilter('seller_id', $seller->getId());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

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
    public function delete(ChatMessageInterface $chatMessage)
    {
        try {
            $chatMessageModel = $this->chatMessageFactory->create();
            $this->resource->load($chatMessageModel, $chatMessage->getMessageId());
            $this->resource->delete($chatMessageModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the ChatMessage: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param int $customerId
     * @param $messageId
     * @return bool
     * @throws LocalizedException
     */
    public function sellerDeleteByMessageId(int $customerId, $messageId)
    {
        return $this->delete($this->sellerGetChatMessage($customerId, $messageId));
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        try {
            $collection = $this->chatMessageCollectionFactory->create();
            foreach ($collection as $key => $model) {
                $model->delete();
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not detete the chat: %1',
                $exception->getMessage()
            ));
        }
    }

    /**
     * @param int $customerId
     * @param int $chatId
     * @param ChatMessageInterface $message
     * @return ChatMessageInterface|void
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function sellerSendCustomerChatMessage(
        int $customerId,
        int $chatId,
        \Lofmp\ChatSystem\Api\Data\ChatMessageInterface $message
    ) {

        if (!$message->getBodyMsg()) {
            throw new CouldNotDeleteException(__(
                'body_msg is required.'
            ));
        }
        $seller = $this->getSellerByCustomerId($customerId);
        if ($seller & $seller->getId()) {
            $chatModel = $this->chatRepository->getById($chatId);
            if (!$chatModel->getId()) {
                throw new CouldNotSaveException(__(
                    'Chat is not exists.'
                ));
            }
            if ($message->getData('seller_id') == $seller->getId()) {
                $data['chat_id'] = $message['chat_id'];
                $data['seller_id'] = $seller->getId();
                $data['customer_email'] = $message->getData('customer_email');
                $data['customer_name'] = $message->getData('customer_name');
                $data['body_msg'] = $message['body_msg'];
                $data = $this->_helper->xss_clean_array($data);
                try {
                    $message->setData($data);
                    $this->resource->save($message);
                } catch (\Exception $exception) {
                    throw new CouldNotSaveException(__(
                        'Could not save the chat: %1',
                        $exception->getMessage()
                    ));
                }
            }
        } else {
            throw new CouldNotSaveException(__(
                'Seller is not available.'
            ));
        }
    }

    /**
     * get seller by sellerUrl
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
