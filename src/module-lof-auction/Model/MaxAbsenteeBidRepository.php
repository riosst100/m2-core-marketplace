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

use Lof\Auction\Api\AmountRepositoryInterface;
use Lof\Auction\Api\AutoAuctionRepositoryInterface;
use Lof\Auction\Api\MaxAbsenteeBidRepositoryInterface;
use Lof\Auction\Api\Data\MaxAbsenteeBidInterface;
use Lof\Auction\Api\Data\MaxAbsenteeBidInterfaceFactory;
use Lof\Auction\Model\ResourceModel\MaxAbsenteeBid as ResourceMaxAbsenteeBid;
use Lof\Auction\Api\Data\MaxAbsenteeBidSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Helper\Data;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Class MaxAbsenteeBidRepository
 * @package Lof\Auction\Model
 */
class MaxAbsenteeBidRepository implements MaxAbsenteeBidRepositoryInterface
{
    /**
     * @var MaxAbsenteeBidFactory
     */
    protected $modelObjectFactory;

    /**
     * @var ResourceMaxAbsenteeBid
     */
    protected $resource;

    /**
     * @var ProductRepository
     */
    protected $auctionRepository;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var DataSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var AutoAuctionRepositoryInterface
     */
    protected $autoAuctionRepository;

    /**
     * @var AmountRepositoryInterface
     */
    protected $amountAuctionRepository;

    /**
     * @var int
     */
    protected $_currentCustomerId = 0;

    /**
     * @var bool
     */
    protected $_ignoreCheckCustomer = false;

    /**
     * @var mixed|Object|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    protected $_maxAbsenteeList = null;

    /**
     * Initialize resource model
     *
     * @param MaxAbsenteeBidFactory $modelObjectFactory
     * @param ResourceMaxAbsenteeBid $resource
     * @param ProductRepository $auctionRepository
     * @param Data $helperData
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param AutoAuctionRepositoryInterface $autoAuctionRepository
     * @param AmountRepositoryInterface $amountAuctionRepository
     */
    public function __construct(
        MaxAbsenteeBidFactory $modelObjectFactory,
        ResourceMaxAbsenteeBid $resource,
        ProductRepository $auctionRepository,
        Data $helperData,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        AutoAuctionRepositoryInterface $autoAuctionRepository,
        AmountRepositoryInterface $amountAuctionRepository
    ) {
        $this->modelObjectFactory = $modelObjectFactory;
        $this->resource = $resource;
        $this->auctionRepository = $auctionRepository;
        $this->helperData = $helperData;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->autoAuctionRepository = $autoAuctionRepository;
        $this->amountAuctionRepository = $amountAuctionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->modelObjectFactory->create()->getCollection();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getMyList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->modelObjectFactory->create()->getCollection();

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("customer_id", $customerId);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }


    /**
     * {@inheritdoc}
     */
    public function getById($entityId)
    {
        $dataModel = $this->modelObjectFactory->create();
        $dataModel->load($entityId);
        if (!$dataModel->getId()) {
            throw new NoSuchEntityException(__('Record with id "%1" does not exist.', $entityId));
        }
        return $dataModel->getDataModel();
    }

    /**
     * Set current customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function setCurrentCustomerId($customerId)
    {
        $this->_currentCustomerId = $customerId;
        return $this;
    }

    /**
     * get current customer id
     *
     * @return int
     */
    public function getCurrentCustomerId()
    {
        return $this->_currentCustomerId;
    }

    /**
     * set setIgnoreCheckCustomer
     *
     * @param bool $flag
     * @return $this
     */
    public function setIgnoreCheckCustomer($flag = false)
    {
        $this->_ignoreCheckCustomer = $flag;
        return $this;
    }

    /**
     * set max absentee bid list
     *
     * @param array|mixed $list
     * @return $this
     */
    public function setMaxAbsenteeBidList($list = null)
    {
        $this->_maxAbsenteeList = $list;
        return $this;
    }

    /**
     * Get available max absentee bid
     *
     * @param int $auctionId
     * @param int $customerId
     * @param float|int $amount
     *
     * @return mixed|Object|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getAvailableMaxAbsenteeBid($auctionId, $customerId, $amount)
    {
        $auctionConfig = $this->helperData->getAuctionConfiguration();
        $isAutoEnabled = $auctionConfig['auto_enable']
                && $auctionConfig['enable_max_absentee_bid'];
        if (!$isAutoEnabled) {
            return null;
        }
        $collection = $this->modelObjectFactory->create()->getCollection();
        if (!$this->_ignoreCheckCustomer) {
            $collection->addFieldToFilter("customer_id", ["neq" => $customerId]);
        }

        $collection->addFieldToFilter("status", 1)
                                ->addFieldToFilter("auction_id", $auctionId)
                                ->addFieldToFilter("max_absentee_amount", ["gt" => (float)$amount])
                                ->setOrder("max_absentee_amount", 'ASC') //sort by max absentee amount low to hight value
                                ->setPageSize(200); //only allow max 200 records
        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function createAutoBid(
        $bid_id,
        $isAuto = true
    ) {
        $auctionConfig = $this->helperData->getAuctionConfiguration();
        $isAutoEnabled = $auctionConfig['auto_enable']
                && $auctionConfig['enable_max_absentee_bid'];
        if (!$isAutoEnabled) {
            return false;
        }
        try {
            if ($isAuto) {
                $biddingModel = $this->autoAuctionRepository->getById((int)$bid_id);
                $amount = $biddingModel->getAmount();
            } else {
                $biddingModel = $this->amountAuctionRepository->getById((int)$bid_id);
                $amount = $biddingModel->getAuctionAmount();
            }
            if ($biddingModel->getStatus() == 1 && $biddingModel->getIsSpam() == 0) {
                $auctionId = $biddingModel->getAuctionId();
                $customerId = $biddingModel->getCustomerId();
                $productId = $biddingModel->getProductId();
                $auctionProduct = $this->auctionRepository->getById($auctionId);

                if ($auctionProduct->getAutoAuctionOpt() && $auctionProduct->getAuctionStatus() == 1 && $this->validateAuctionDate($auctionProduct)) {
                    if ($this->_maxAbsenteeList) {
                        $collection = $this->_maxAbsenteeList;
                        $this->setMaxAbsenteeBidList(null);
                    } else {
                        $collection = $this->getAvailableMaxAbsenteeBid($auctionId, $customerId, $amount);
                    }

                    if ($total = $collection->count()) {
                        //1. Start
                        $oldStatus = $auctionProduct->getStatus();
                        //Hoding processing Auction when place out bid
                        $this->auctionRepository->updateAuctionStatus($auctionProduct, AuctionStatus::STATUS_PROCESS_HODING);
                        $couting = 1;
                        $lastBiddingId = false;
                        foreach ($collection as $_item) {
                            $minBidAmountArr = $this->helperData->getMinAmount($auctionId, $auctionConfig, true);
                            $minBidAmount = $minBidAmountArr["minAmount"];
                            $flag = true;
                            $currentCustomerId = $minBidAmountArr["customerId"];
                            if ($total > 1 && $currentCustomerId == $_item->getData("customer_id")) {
                                $flag = false;
                            }

                            if ((float)$minBidAmount <= (float)$_item->getData("max_absentee_amount") && $flag) {
                                $response = $this->amountAuctionRepository
                                    ->setIsCheckContinues(false)
                                    ->setAllowSendEmail(false)
                                    ->setIsAllowCallback(false)
                                    ->setIsNotSaveHistory(true)
                                    ->setCallBackCreateAutoBidObject($this)
                                    ->placeBid($_item->getData("customer_id"), $productId, $minBidAmount, true);

                                $lastBiddingId = isset($response["biddingId"]) && $response["biddingId"] ? (int)$response["biddingId"]: $lastBiddingId;
                            }
                            $couting++;
                        }
                        if ($lastBiddingId && $total > 1) {
                            $this->setIgnoreCheckCustomer(true)->createAutoBid($lastBiddingId, true);
                        }
                        //TODO: set max bid amount for absentee list of first load: $collection
                        //2. End
                        //Hoding processing Auction when place out bid
                        $this->auctionRepository->updateAuctionStatus($auctionProduct, $oldStatus);
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * process update bid amount with max absentee amount
     * @param int $auctionId
     * @return bool
     */
    public function processUpdateBid($auctionId)
    {
        $auctionConfig = $this->helperData->getAuctionConfiguration();
        $isAutoEnabled = $auctionConfig['auto_enable']
                && $auctionConfig['enable_max_absentee_bid'];
        if (!$isAutoEnabled) {
            return false;
        }
        if ($this->_maxAbsenteeList && $auctionId) {
            $auctionProductNew = $this->auctionRepository->getById($auctionId);
            $minAuctionAmount = $auctionProductNew->getMinAmount();

            $collection = $this->_maxAbsenteeList->addFieldToFilter("max_absentee_amount", ["lt", (float)$minAuctionAmount]);

            foreach ($collection as $_item) {
                if ((float)$_item->getData("max_absentee_amount") < (float)$minAuctionAmount) {
                    $autoBid = $this->autoAuctionRepository->getCurrentCustomerAuctionBid($_item->getData("customer_id"), $auctionId);
                    if ($autoBid && (int)$autoBid->getData("entity_id") && (float)$autoBid->getAmount() < (float)$_item->getData("max_absentee_amount")) {

                        $this->autoAuctionRepository->updateAutoBidAmount($autoBid->getData("entity_id"), (float)$_item->getData("max_absentee_amount"));

                        $this->autoAuctionRepository->updateMixBidAmount($autoBid->getData("entity_id"), (float)$_item->getData("max_absentee_amount"), "bid_id");
                    }
                }
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxBidAmount(
        $customerId,
        $auction_id
    ) {
        $model = $this->modelObjectFactory->create();
        $collection = $model->getCollection()
                ->addFieldToFilter("customer_id", $customerId)
                ->addFieldToFilter("auction_id", $auction_id)
                ->addFieldToFilter("status", 1);
        $foundItem = $collection->getFirstItem();
        if ($foundItem) {
            $this->resource->load($model, $foundItem->getId());
        }

        if (!$model->getId()) {
            throw new NoSuchEntityException(__('MaxAbsenteeBid with customer ID "%1" and Auction ID "%2" does not exist.', $customerId, $auction_id));
        }
        return $model->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxBidAmount(
        $customerId,
        $auction_id,
        $max_bid_amount
    ) {
        $auction = $this->auctionRepository->getById($auction_id);
        if (!$auction || !$auction->getEntityId()) {
            throw new NoSuchEntityException(__('Auction with auction ID "%1" does not exist.', $auction_id));
        }
        if ($auction->getAuctionStatus() != 1) {
            throw new NoSuchEntityException(__('Auction with auction ID "%1" is not available at now.', $auction_id));
        }
        if (!$this->validateAuctionDate($auction)) {
            throw new NoSuchEntityException(__('Auction with ID "%1" is expired!', $auction_id));
        }
        if ($this->helperData->isRequirePlaceBid()) {
            $flagChecked = $this->processCreateFirstBid($customerId, $auction_id, $auction->getProductId(), $max_bid_amount);
            if ($flagChecked) {
                $auction = $this->auctionRepository->getById($auction_id);
            }
        }
        if (!$this->validateBidder($customerId, $auction_id)) {
            throw new NoSuchEntityException(__('You should place a bid for auction ID "%1" before.', $auction_id));
        }

        $minAmount = (float)$auction->getMinAmount();
        $maxAmount = (float)$auction->getMaxAmount();
        if ($maxAmount && $max_bid_amount > $maxAmount) {
            throw new NoSuchEntityException(__('Your max bid amount should greater than current auction bid amount. Please try again!'));
        }
        if ($minAmount && $max_bid_amount < $minAmount) {
            throw new NoSuchEntityException(__('Your max bid amount should greater than current auction bid amount. Please try again!'));
        }

        $foundModelData = [];
        try {
            $dataObject = $this->getMaxBidAmount($customerId, $auction_id);
            $dataObject->setMaxAbsenteeAmount($max_bid_amount);

            $foundModelData = $this->extensibleDataObjectConverter->toNestedArray(
                $dataObject,
                [],
                \Lof\Auction\Api\Data\MaxAbsenteeBidInterface::class
            );
        } catch (\Exception $exception) {

            $foundModelData = [
                "product_id" => $auction->getProductId(),
                "auction_id" => $auction_id,
                "customer_id" => $customerId,
                "max_absentee_amount" => $max_bid_amount
            ];
        }

        $model = $this->modelObjectFactory->create()->setData($foundModelData);

        try {
            $this->resource->save($model);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the MaxAbsenteeBid: %1',
                $exception->getMessage()
            ));
        }
        return $model->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entityId)
    {
        try {
            $dataModel = $this->modelObjectFactory->create();
            $dataModel->load($entityId);
            $dataModel->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the MaxAbsenteeBid: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMySubscription($customerId, $entityId)
    {
        try {
            $dataModel = $this->modelObjectFactory->create();
            $dataModel->load($entityId);
            if ($dataModel->getCustomerId() == $customerId) {
                $dataModel->delete();
            } else {
                throw new CouldNotDeleteException(__(
                    'Could not delete the not available MaxAbsenteeBid: %1', $entityId
                ));
                return false;
            }
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the MaxAbsenteeBid: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function flushLowerAbsenteeBid($auction_id)
    {
        try {
            $auction = $this->auctionRepository->getById($auction_id);
            if (!$auction || !$auction->getEntityId()) {
                throw new NoSuchEntityException(__('Auction with auction ID "%1" does not exist.', $auction_id));
            }
            $minAmount = $auction->getMinAmount();
            $collection = $this->modelObjectFactory->create()->getCollection()
                                ->addFieldToFilter("auction_id", $auction_id)
                                ->addFieldToFilter("max_absentee_amount", ["lt" => (float)$minAmount]);

            if ($collection->count()) {
                foreach ($collection as $_item) {
                    $_item->delete();
                }
            }
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the MaxAbsenteeBid: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function flushByCustomer($customer_id, $auction_id)
    {
        try {
            $collection = $this->modelObjectFactory->create()->getCollection()
                                ->addFieldToFilter("customer_id", $customer_id)
                                ->addFieldToFilter("auction_id", $auction_id);

            if ($collection->count()) {
                foreach ($collection as $_item) {
                    $_item->delete();
                }
            }
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the MaxAbsenteeBid: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Validate auction date is running or not
     *
     * @param Object $auction
     * @return bool|int
     */
    protected function validateAuctionDate($auction)
    {
        $today = $this->helperData->getTimezoneDateTime();
        $startAuctionTime = $this->helperData->getTimezoneDateTime($auction->getStartAuctionTime());
        $stopAuctionTime = $this->helperData->getTimezoneDateTime($auction->getStopAuctionTime());
        $difference = strtotime($stopAuctionTime) - strtotime($today);
        if ($difference > 0 && $startAuctionTime < $today) {
            return true;
        }
        return false;
    }

    /**
     * Validate current customer is bidded the auction before or not. If not, dont allow he set max absentee bid
     *
     * @param int $customer_id
     * @param int $auction_id
     * @param bool $checkAutoOnly
     * @return bool|int
     */
    protected function validateBidder($customer_id, $auction_id, $checkAutoOnly = false)
    {
        if ($this->helperData->isRequirePlaceBid()) {
            $countManual = 0;
            $countAuto = 0;

            $findAutoBid = $this->amountAuctionRepository->getAutoCollection();
            $findAutoBid->addFieldToFilter("customer_id", $customer_id)
                    ->addFieldToFilter("auction_id", $auction_id);
            $countAuto = $findAutoBid->count();

            if (!$checkAutoOnly) {
                $findManualBid = $this->amountAuctionRepository->getAmountCollection();
                $findManualBid->addFieldToFilter("customer_id", $customer_id)
                        ->addFieldToFilter("auction_id", $auction_id);
                $countManual = $findManualBid->count();
            }
            return ($countManual || $countAuto) ? true : false;
        }
        return true;
    }

    /**
     * Validate current customer is bidded the auction before or not. If not, dont allow he set max absentee bid
     *
     * @param int $customer_id
     * @param int $auction_id
     * @param int $productId
     * @param float|int $max_absentee_amount
     * @return bool|int
     */
    protected function processCreateFirstBid($customer_id, $auctionId, $productId, $max_absentee_amount)
    {
        $auctionConfig = $this->helperData->getAuctionConfiguration();
        $isAutoEnabled = $auctionConfig['auto_enable']
                && $auctionConfig['enable_max_absentee_bid'];
        if (!$isAutoEnabled) {
            return false;
        }
        $minBidAmountArr = $this->helperData->getMinAmount($auctionId, $auctionConfig, true);
        $minBidAmount = $minBidAmountArr["minAmount"];

        if ((float)$minBidAmount <= (float)$max_absentee_amount) {
            if ($this->helperData->getConfig("auto/run_other_absentee")) {
                $this->amountAuctionRepository
                                ->setIsCheckContinues(false)
                                ->setIsAllowCallback(true)
                                ->setCallBackCreateAutoBidObject($this)
                                ->placeBid($customer_id, $productId, $minBidAmount, true);
            } else {
                $this->amountAuctionRepository
                                ->setIsCheckContinues(false)
                                ->setIsAllowCallback(false)
                                ->placeBid($customer_id, $productId, $minBidAmount, true);
            }

            return true;
        }
        return false;
    }
}
