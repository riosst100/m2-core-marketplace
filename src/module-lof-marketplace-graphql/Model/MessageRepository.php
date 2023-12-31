<?php

/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Model;

use Lof\MarketplaceGraphQl\Api\MessageRepositoryInterface;
use Lof\MarketplaceGraphQl\Api\Data\MessageInterface;
use Lof\MarketplaceGraphQl\Api\Data\MessageInterfaceFactory;
use Lof\MarketplaceGraphQl\Api\Data\MessageSearchResultsInterfaceFactory;
use Lof\MarketPlace\Model\ResourceModel\Message\CollectionFactory as MessageCollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory as MessageDetailCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class MessageRepository
 */
class MessageRepository implements MessageRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * SellerMessageRepository constructor.
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param MessageDetailCollectionFactory $messageDetailCollectionFactory
     * @param MessageInterfaceFactory $dataMessageFactory
     * @param MessageSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        MessageCollectionFactory $messageCollectionFactory,
        MessageDetailCollectionFactory $messageDetailCollectionFactory,
        MessageInterfaceFactory $dataMessageFactory,
        MessageSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Helper\Data $helper,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->messageDetailCollectionFactory = $messageDetailCollectionFactory;
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->helper = $helper;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->dataMessageFactory = $dataMessageFactory;
;
    }

    /**
     * {@inheritdoc}
     */
    public function getListSellerMessages(
        int $sellerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->messageCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\MarketplaceGraphQl\Api\Data\MessageInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("owner_id", $sellerId);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $key => $model) {
            $items[] = $this->getDataModel($model->getData());
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListMessages(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    ) {
        $collection = $this->messageCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\MarketplaceGraphQl\Api\Data\MessageInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        $collection->addFieldToFilter("sender_id", $customerId);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $items = [];
        foreach ($collection as $key => $model) {
            $items[] = $this->getDataModel($model->getData());
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * convert array data to object
     *
     * @param array|mixed $data
     * @return MessageInterface
     */
    public function getDataModel($data = [])
    {
        $dataObject = $this->dataMessageFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $dataObject,
            $data,
            MessageInterface::class
        );

        return $dataObject;
    }
}
