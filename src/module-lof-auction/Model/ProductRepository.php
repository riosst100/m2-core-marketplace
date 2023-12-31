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

use Lof\Auction\Api\ProductRepositoryInterface;
use Lof\Auction\Api\Data\ProductSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Api\Data\ProductInterfaceFactory as DataInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Lof\Auction\Model\ResourceModel\Product as ResourceProduct;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory as ModelCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Catalog\Api\ProductRepositoryInterface as CoreProductRepositoryInterface;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Helper\Data as AuctionData;

/**
 * Class ProductRepository
 * @package Lof\Auction\Model
 */
class ProductRepository implements ProductRepositoryInterface
{

    /**
     * @var ResourceProduct
     */
    protected $resource;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ModelCollectionFactory
     */
    protected $modelCollectionFactory;

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
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var CoreProductRepositoryInterface
     */
    protected $coreProductRepository;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

    /**
     * @var AutoAuctionFactory
     */
    protected $autoAuctionFactory;

    /**
     * @var AuctionData
     */
    protected $helperData;

    /**
     * @var mixed
     */
    protected $auctionsBySku = [];

    /**
     * @param ResourceProduct $resource
     * @param ProductFactory $productFactory
     * @param DataInterfaceFactory $dataApiFactory
     * @param ModelCollectionFactory $modelCollectionFactory
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceConnection $resourceConnection
     * @param CoreProductRepositoryInterface $coreProductRepository
     * @param AmountFactory $amountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param AuctionData $helperData
     */
    public function __construct(
        ResourceProduct $resource,
        ProductFactory $productFactory,
        DataInterfaceFactory $dataApiFactory,
        ModelCollectionFactory $modelCollectionFactory,
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ResourceConnection $resourceConnection,
        CoreProductRepositoryInterface $coreProductRepository,
        AmountFactory $amountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        AuctionData $helperData
    ) {
        $this->resource = $resource;
        $this->productFactory = $productFactory;
        $this->dataApiFactory = $dataApiFactory;
        $this->modelCollectionFactory = $modelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->_resource = $resourceConnection;
        $this->coreProductRepository = $coreProductRepository;
        $this->amountFactory = $amountFactory;
        $this->autoAuctionFactory = $autoAuctionFactory;
        $this->helperData = $helperData;
    }

    /**
     * Save Product data
     *
     * @param \Lof\Auction\Api\Data\ProductInterface $product
     * @return Product
     * @throws CouldNotSaveException
     */
    public function save(\Lof\Auction\Api\Data\ProductInterface $product)
    {
        $dataModel = $this->productFactory->create();
        if ($dataModel->getEntityId()) {
            $dataModel->load((int)$dataModel->getEntityId());
        }
        try {
            $dataModel->setData((array)$product->getData())->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auction product: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * Update Product data
     *
     * @param int $entityId
     * @param \Lof\Auction\Api\Data\ProductInterface $product
     * @return Product
     * @throws CouldNotSaveException
     */
    public function update($entityId, \Lof\Auction\Api\Data\ProductInterface $product)
    {
        $dataModel = $this->productFactory->create()->getCollection()->addFieldToFilter('entity_id', $entityId);
        if ($dataModel) {
            $dataModel = $this->productFactory->create()->load($entityId)->setData((array)$product->getData())->save();
        } else {
            throw new CouldNotSaveException(__(
                'Could not save the auction product: %1'
            ));
        }
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($entityId)
    {
        $dataModel = $this->productFactory->create();
        $this->resource->load($dataModel, $entityId);
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getByProductSku($sku, $storeId = null)
    {
        if (!isset($this->auctionsBySku[$sku])) {
            $product = $this->coreProductRepository->get($sku, $storeId);
            $auctionId = 0;
            $auctionProduct = null;
            $foundItem = $this->productFactory
                                ->create()
                                ->getCollection()
                                ->addFieldToFilter('product_id', $product->getId())
                                ->addFieldToFilter('auction_status',
                                    [
                                        'in' => [
                                            AuctionStatus::STATUS_TIME_END,
                                            AuctionStatus::STATUS_PROCESS,
                                            AuctionStatus::STATUS_WINNER_NOT_DEFINED
                                        ]
                                    ]
                                )
                                ->setOrder('entity_id')
                                ->getFirstItem();

            if ($foundItem && $foundItem->getId()) {
                $auctionProduct = $this->getById($foundItem->getId());
            }
            if ($auctionProduct) {
                $auctionId = $auctionProduct->getEntityId();
                $today = $this->helperData->getTimezoneDateTime();
                $stop_auction_utc_time = $auctionProduct->getStopAuctionTime();
                $start_auction_utc_time = $auctionProduct->getStartAuctionTime();
                $min_amount = $auctionProduct->getMinAmount();
                $starting_price = $auctionProduct->getStartingPrice();
                $start_auction_time = $this->helperData->getTimezoneDateTime($auctionProduct->getStartAuctionTime());
                $stop_auction_time = $this->helperData->getTimezoneDateTime($auctionProduct->getStopAuctionTime());
                $new_auction_start = false;

                $auctionProduct->setTimeZone($this->helperData->getTimezoneName());
                $auctionProduct->setStopAuctionUtcTime($stop_auction_utc_time);
                $auctionProduct->setStartAuctionUtcTime($start_auction_utc_time);
                $auctionProduct->setTodayTime($today);
                $auctionProduct->setCurrentTimeStamp(strtotime($today));
                $auctionProduct->setStartAuctionTimeStamp(strtotime($start_auction_time));
                $auctionProduct->setStopAuctionTimeStamp(strtotime($stop_auction_time));
                $auctionProduct->setStartAuctionTime($start_auction_time);
                $auctionProduct->setStopAuctionTime($stop_auction_time);
                $auctionProduct->setProName($product->getName());
                $auctionProduct->setProUrl($product->getUrlKey());

                if ($auctionProduct->getCustomerId()) {
                    $bidder = $this->helperData->getCustomerById($auctionProduct->getCustomerId());
                    $bidderName = $this->helperData->getBidderName($bidder);
                    $auctionProduct->setCustomerName($bidderName);
                }

                if ($min_amount < $starting_price) {
                    $auctionProduct->setMinAmount($starting_price);
                }

                $aucAmtData = $this->amountFactory->create()->getCollection()
                            ->addFieldToFilter('product_id', ['eq' => $product->getId()])
                            ->addFieldToFilter('auction_id', ['eq'=> $auctionId])
                            ->addFieldToFilter('winning_status', ['eq' => 1])
                            ->addFieldToFilter('shop', ['neq' => 0])
                            ->getFirstItem();

                if ($aucAmtData->getEntityId()) {
                    $aucAmtData = $this->autoAuctionFactory->create()->getCollection()
                        ->addFieldToFilter('auction_id', ['eq'=> $auctionId])
                        ->addFieldToFilter('flag', ['eq' => 1])
                        ->addFieldToFilter('shop', ['neq' => 0])
                        ->getFirstItem();
                }
                $new_auction_start = $aucAmtData->getEntityId() ? true : false;
                $auctionProduct->setNewAuctionStart($new_auction_start);
            }
            $this->auctionsBySku[$sku] = $auctionProduct;
        }
        return $this->auctionsBySku[$sku];
    }

    /**
     * {@inheritdoc}
     */
    public function updateAuctionStatus($dataModel, $status = null)
    {
        try {
            if ($dataModel && $status !== null) {
                $dataModel->setStatus((int)$status);
                $dataModel->save();
            }
        } catch (\Exception $exception) {
            return false;
        }

        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->modelCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("auction_status", AuctionStatus::STATUS_PROCESS);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entityId)
    {
        try {
            $dataModel = $this->productFactory->create();
            $dataModel->load($entityId);
            $dataModel->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Auction Product: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        $dataModel = $this->getById($entityId);
        return $this->delete($dataModel);
    }
}
