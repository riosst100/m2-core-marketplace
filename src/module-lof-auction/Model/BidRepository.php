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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Model;

use Lof\Auction\Api\BidRepositoryInterface;
use Lof\Auction\Api\AutoAuctionRepositoryInterface;
use Lof\Auction\Api\AmountRepositoryInterface;
use Lof\Auction\Api\ProductRepositoryInterface;
use Lof\Auction\Api\Data\MixBidInterfaceFactory as DataInterfaceFactory;
use Lof\Auction\Api\Data\MixBidSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\History as HistoryHelper;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Lof\Auction\Model\ResourceModel\MixAmount\Collection;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;

/**
 * Class BidRepository
 * @package Lof\Auction\Model
 */
class BidRepository implements BidRepositoryInterface
{

    /**
     * @var AutoAuctionRepositoryInterface
     */
    protected $autoAuctionRepository;

    /**
     * @var AmountRepositoryInterface
     */
    protected $amountRepository;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DataSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var DataInterfaceFactory
     */
    protected $dataApiFactory;

    /**
     * @var
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var HistoryHelper
     */
    private $_historyHelper;

    /**
     * @var MixAmountCollectionFactory
     */
    private $modelCollectionFactory;

    /**
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Data $helperData
     * @param ProductRepositoryInterface $productRepository
     * @param MixAmountCollectionFactory $mixAmountCollectionFactory
     */
    public function __construct(
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        Data $helperData,
        ProductRepositoryInterface $productRepository,
        MixAmountCollectionFactory $mixAmountCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
        $this->modelCollectionFactory = $mixAmountCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        $entityId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->modelCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter('auction_id', $entityId)
            ->addFieldToFilter('is_spam', 0)
            ->setOrder('amount', 'DESC');

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];

        foreach ($collection->getItems() as $_item) {
            $customer = $this->helperData->getCustomerById($_item->getCustomerId());
            if ($customer) {
                $customer_name = $this->helperData->getBidderName($customer);
                $_item->setCustomerName($customer_name);
            }
            $_item->setUtcCreatedAt($this->helperData->convertUtcToStoreTime($_item->getCreatedAt()));
            $items[] = $_item;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

}
