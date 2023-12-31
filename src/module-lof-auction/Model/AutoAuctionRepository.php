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
use Lof\Auction\Api\Data\AutoAuctionInterfaceFactory as DataInterfaceFactory;
use Lof\Auction\Api\Data\AutoAuctionSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Model\ResourceModel\AutoAuction as ResourceAutoAuction;
use Lof\Auction\Model\ResourceModel\AutoAuction\CollectionFactory as ModelCollectionFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\Email;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Helper\History as HistoryHelper;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AutoAuctionRepository
 * @package Lof\Auction\Model
 */
class AutoAuctionRepository implements AutoAuctionRepositoryInterface
{

    /**
     * @var ResourceAutoAuction
     */
    protected $resource;

    /**
     * @var AutoAuctionFactory
     */
    protected $autoauctionFactory;

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
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var HistoryHelper $_historyFactory
     */
    private $_historyHelper;

    /**
     * @var MixAmountCollectionFactory
     */
    private $_mixAmountCollectionFactory;

    /**
     * @var Email
     */
    private $_helperEmail;

    /**
     * @var MixAmountFactory
     */
    protected $_mixAmountFactory;

    /**
     * @var AmountRepositoryInterface
     */
    protected $_amountRepository;

    /**
     * @param ResourceAutoAuction $resource
     * @param AutoAuctionFactory $autoauctionFactory
     * @param DataInterfaceFactory $dataApiFactory
     * @param ModelCollectionFactory $modelCollectionFactory
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param Data $helperData
     * @param ProductFactory $auctionProductFactory
     * @param HistoryHelper $historyHelper
     * @param MixAmountCollectionFactory $mixAmountCollectionFactory
     * @param Email $helperEmail
     * @param MixAmountFactory $mixAmountFactory
     * @param AmountRepositoryInterface $amountRepository
     */
    public function __construct(
        ResourceAutoAuction $resource,
        AutoAuctionFactory $autoauctionFactory,
        DataInterfaceFactory $dataApiFactory,
        ModelCollectionFactory $modelCollectionFactory,
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        Data $helperData,
        ProductFactory $auctionProductFactory,
        HistoryHelper $historyHelper,
        MixAmountCollectionFactory $mixAmountCollectionFactory,
        Email $helperEmail,
        MixAmountFactory $mixAmountFactory,
        AmountRepositoryInterface $amountRepository
    ) {
        $this->resource = $resource;
        $this->autoauctionFactory = $autoauctionFactory;
        $this->dataApiFactory = $dataApiFactory;
        $this->modelCollectionFactory = $modelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->_resource = $resourceConnection;
        $this->helperData = $helperData;
        $this->_auctionProductFactory = $auctionProductFactory;
        $this->_historyHelper = $historyHelper;
        $this->_mixAmountCollectionFactory = $mixAmountCollectionFactory;
        $this->_helperEmail = $helperEmail;
        $this->_mixAmountFactory = $mixAmountFactory;
        $this->_amountRepository = $amountRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save($autoauction)
    {
        $dataModel = $this->autoauctionFactory->create();
        if ($dataModel->getEntityId()) {
            $dataModel->load((int)$dataModel->getEntityId());
        }
        try {
            $dataModel->setData((array)$autoauction);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auto auction bid: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($entityId)
    {
        $dataModel = $this->autoauctionFactory->create();
        $dataModel->load($entityId);
        if (!$dataModel->getId()) {
            throw new NoSuchEntityException(__('Auto Auction Bid with id "%1" does not exist.', $entityId));
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
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getListByAuctionId( $entity_id, \Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $collection = $this->modelCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);
        $collection->addFieldToFilter("auction_id", $entity_id);
        $collection->addFieldToFilter("is_spam", 0);
        $collection->setOrder('amount', 'DESC');

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
            $dataModel = $this->autoauctionFactory->create();
            $dataModel->load($entityId);
            $dataModel->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Auto Auction Bid: %1',
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

    /**
     * {@inheritdoc}
     */
    public function placeAutoBid($customerId, int $productId)
    {
        if (!$productId) {
            throw new CouldNotSaveException(__(
                'Could not place auto bid for the auction. Missing required field product_id. Please try again!'
            ));
        }
        $bidData = $this->_manualRepository->placeBid($customerId, (int)$productId, 0, false);
        if (isset($bidData['biddingId']) && $bidData['biddingId']) {
            return $this->getById($bidData['biddingId']);
        } else {
            throw new CouldNotSaveException(__(
                'Could not place auto bid for the auction.'
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveAutoBid($customerId, $autoauction)
    {
        if (!$autoauction->getAuctionId()) {
            throw new CouldNotSaveException(__(
                'Auto Auction Bid require Auction ID'
            ));
        }
        $auctionModel = $this->_auctionProductFactory->create()->load($autoauction->getAuctionId());
        if (!$auctionModel || ($auctionModel && !$auctionModel->getEntityId())) {
            throw new NoSuchEntityException(__('Auction with auction ID "%1" does not exist.', $autoauction->getAuctionId()));
        }
        if ($auctionModel->getAuctionStatus() != 1) {
            throw new NoSuchEntityException(__('Auction with auction ID "%1" is not available at now.', $autoauction->getAuctionId()));
        }
        if (!$this->validateAuctionDate($auctionModel)) {
            throw new NoSuchEntityException(__('Auction with ID "%1" is expired!', $autoauction->getAuctionId()));
        }
        $userId = $customerId;
        $dataModel = $this->autoauctionFactory->create();
        $autoauction->setCustomerId((int)$customerId);

        try {
            $data = [
                "auction_id" => $autoauction->getAuctionId(),
                "amount" => $autoauction->getAmount(),
                "product_id" => $auctionModel->getProductId()
            ];
            $val = $data['amount'];

            $auctionConfig = $this->helperData->getAuctionConfiguration();

            $autoBidRecord = $this->autoauctionFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('auction_id', $data['auction_id'])
                ->addFieldToFilter('status', '1')
                ->getFirstItem();

            if ($autoBidRecord->getEntityId()) {
                if (!$auctionConfig['auto_auc_limit']) {
                    throw new CouldNotSaveException(__(
                        'You are not allowed to auto bid again.'
                    ));
                } else {
                    $autoBidRecord->setId($autoBidRecord->getEntityId());
                    $autoBidRecord->setAmount($val);
                    $autoBidRecord->setIsSpam(0);
                    $autoBidRecord->setCreatedAt($this->helperData->getDateTime()->date('Y-m-d H:i:s'));
                    $autoBidRecord->save();
                    $this->_historyHelper->saveHistory($autoBidRecord);
                    $dataModel = $this->autoauctionFactory->create()->load($autoBidRecord->getEntityId());
                }
            } else {
                $dataModel->setAmount($val);
                $dataModel->setCustomerId($customerId);
                $dataModel->setProductId($data['product_id']);
                $dataModel->setAuctionId($data['auction_id']);
                $dataModel->setStatus(1);
                $dataModel->setIsSpam(0);
                $dataModel->save();
                $this->_historyHelper->saveHistory($dataModel);
            }
            $this->sendOutBid($data, $auctionConfig, $val, $userId);
            if ($auctionConfig['enable_outbid_email']) {
                $listForSendMail = $this->_mixAmountCollectionFactory->create()
                                        ->addFieldToFilter('auction_id', $data['auction_id']);

                foreach ($listForSendMail as $key) {
                    $customerId = $key->getCustomerId();
                    if ($customerId != $userId && $customerId) {
                        $this->_helperEmail->sendMailToMembers($customerId, $userId, $data['product_id']);
                    }
                }
            }

            if ($auctionConfig['enable_admin_email']) {
                $this->_helperEmail->sendAutoMailToAdmin($userId, $data['product_id'], $data['amount']);
            }

            $auctionModel->setMinAmount($val);
            $auctionModel->setCustomerId($userId);
            $auctionModel->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auto auction bid: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getMyList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->modelCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);
        $collection->addFieldToFilter("customer_id", (int)$customerId);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * get current customer auction bid
     *
     * @param int $customerId
     * @param int $auction_id
     * @param float|int $amount
     * @return mixed|array
     */
    public function getCurrentCustomerAuctionBid(
        $customerId,
        $auction_id
    ) {
        $collection = $this->modelCollectionFactory->create()
                            ->addFieldToFilter("customer_id", (int)$customerId)
                            ->addFieldToFilter("auction_id", (int)$auction_id);
        return $collection->getFirstItem();
    }

    /**
     * get current customer auction bid
     *
     * @param int $bid_id
     * @return mixed|array
     */
    public function getAuctionMixByBidId(
        $bid_id
    ) {
        $collection = $this->_mixAmountCollectionFactory->create()
                            ->addFieldToFilter("bid_id", (int)$bid_id);
        return $collection->getFirstItem();
    }

    /**
     * update mix bid Amount
     *
     * @param int $entity_id
     * @param float|int $amount
     * @return bool
     */
    public function updateAutoBidAmount(
        $entity_id,
        $amount
    ) {
        $dataModel = $this->autoauctionFactory->create()->load((int)$entity_id);
        try {
            if ($dataModel) {
                $dataModel->setData("amount", $amount);
                $dataModel->save();
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auto auction bid amount: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * update mix bid Amount by auto bid id
     *
     * @param int $entity_id
     * @param float|int $amount
     * @param string $field = "entity_id" or "bid_id"
     * @return bool
     */
    public function updateMixBidAmount(
        $entity_id,
        $amount,
        $field = "entity_id"
    ) {
        $dataModel = $this->_mixAmountFactory->create()->load((int)$entity_id, $field);
        try {
            if ($dataModel) {
                $dataModel->setData("amount", $amount);
                $dataModel->save();
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the mix auction bid amount: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * get current customer auction bid
     *
     * @param int $customerId
     * @param int $auction_id
     * @return mixed|array
     */
    public function getCurrentCustomerAuctionMixBid(
        $customerId,
        $auction_id
    ) {
        $collection = $this->modelCollectionFactory->create()
                            ->addFieldToFilter("customer_id", (int)$customerId)
                            ->addFieldToFilter("auction_id", (int)$auction_id);
        return $collection->getFirstItem();
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
     * @param $data
     * @param $auctionConfig
     * @param $val
     * @param $userId
     */
    protected function sendOutBid($data, $auctionConfig, $val, $userId)
    {
        $totalAmount = $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', $data['auction_id'])
            ->addFieldToFilter('is_spam', 0)
            ->setOrder('amount');
        if (count($totalAmount) && $auctionConfig['enable_outbid_email']) {
            $bid = $totalAmount->getFirstItem();
            $max = $bid->getAmount();
            $customerId = $bid->getCustomerId();
            if ($val > $max) {
                if ($customerId != $userId) {
                    $this->_helperEmail->sendOutBidAutoBidder($customerId, $userId, $data['product_id']);
                }
            }
        }
    }
}
