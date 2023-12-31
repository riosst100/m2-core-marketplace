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

use Exception;
use Lof\Auction\Api\AmountRepositoryInterface;
use Lof\Auction\Api\Data\AmountInterface;
use Lof\Auction\Api\Data\AmountInterfaceFactory as DataInterfaceFactory;
use Lof\Auction\Api\Data\AmountSearchResultsInterface;
use Lof\Auction\Api\Data\AmountSearchResultsInterfaceFactory as DataSearchResultsInterfaceFactory;
use Lof\Auction\Helper\Email;
use Lof\Auction\Model\ResourceModel\Amount as ResourceAmount;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixCollectionFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\History as HistoryHelper;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory as ModelCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AmountRepository
 * @package Lof\Auction\Model
 */
class AmountRepository implements AmountRepositoryInterface
{

    /**
     * @var ResourceAmount
     */
    protected $resource;

    /**
     * @var AmountFactory
     */
    protected $amountFactory;

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
     * @var ProductFactory
     */
    private $auctionFactory;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var AutoAuctionFactory
     */
    private $autoAmountFactory;
    /**
     * @var DateTime
     */
    private $_dateTime;
    /**
     * @var HistoryFactory
     */
    private $_historyFactory;
    /**
     * @var Email
     */
    private $helperEmail;
    /**
     * @var MixCollectionFactory
     */
    private $mixCollectionFactory;

    /**
     * @var HistoryHelper $_historyFactory
     */
    private $_historyHelper;

    /**
     * @var int|bool
     */
    private $_isCheckContinues = true;

    /**
     * @var int|bool
     */
    private $_isAllowSendEmail = true;

    /**
     * @var Object|mixed
     */
    private $_callbackObject = null;

    /**
     * @var int|bool
     */
    private $_isAllowCallback = true;

    /**
     * @var int|bool
     */
    private $_isNotSaveHistory = false;


    /**
     * @param ResourceAmount $resource
     * @param AmountFactory $amountFactory
     * @param AutoAuctionFactory $autoAmountFactory
     * @param DataInterfaceFactory $dataApiFactory
     * @param ModelCollectionFactory $modelCollectionFactory
     * @param DataSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ResourceConnection $resourceConnection
     * @param ProductFactory $auction
     * @param Data $helper
     * @param DateTime $dateTime
     * @param HistoryFactory $historyFactory
     * @param Email $helperEmail
     * @param MixCollectionFactory $mixCollectionFactory
     * @param HistoryHelper $historyHelper
     */
    public function __construct(
        ResourceAmount $resource,
        AmountFactory $amountFactory,
        AutoAuctionFactory $autoAmountFactory,
        DataInterfaceFactory $dataApiFactory,
        ModelCollectionFactory $modelCollectionFactory,
        DataSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        ResourceConnection $resourceConnection,
        ProductFactory $auction,
        Data $helper,
        DateTime $dateTime,
        HistoryFactory $historyFactory,
        Email $helperEmail,
        MixCollectionFactory $mixCollectionFactory,
        HistoryHelper $historyHelper
    ) {
        $this->resource = $resource;
        $this->amountFactory = $amountFactory;
        $this->autoAmountFactory = $autoAmountFactory;
        $this->dataApiFactory = $dataApiFactory;
        $this->modelCollectionFactory = $modelCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->_resource = $resourceConnection;
        $this->auctionFactory = $auction;
        $this->helper = $helper;
        $this->_dateTime = $dateTime;
        $this->_historyFactory = $historyFactory;
        $this->helperEmail = $helperEmail;
        $this->mixCollectionFactory = $mixCollectionFactory;
        $this->_historyHelper = $historyHelper;
    }

    /**
     * Save Product data
     *
     * @param AmountInterface|Amount $amount
     * @return Amount
     * @throws CouldNotSaveException
     */
    public function save(AmountInterface $amount)
    {
        $dataModel = $this->amountFactory->create();
        if ($dataModel->getEntityId()) {
            $dataModel->load((int)$dataModel->getEntityId());
        }
        try {
            $dataModel->setData((array)$amount->getData())->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auction amount: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * Load Page data by given Page Identity
     *
     * @param string $pageId
     * @return Page
     * @throws NoSuchEntityException
     */
    public function getById($entityId)
    {
        $dataModel = $this->amountFactory->create();
        $dataModel->load($entityId);
        if (!$dataModel->getId()) {
            throw new NoSuchEntityException(__('Auction amount with id "%1" does not exist.', $entityId));
        }
        return $dataModel;
    }
    /**
     * @inheritdoc
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
     * Delete Amount
     *
     * @param AmountInterface $page
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete($entityId)
    {
        try {
            $dataModel = $this->amountFactory->create();
            $dataModel->load($entityId);
            $dataModel->delete();
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Auction Amount: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Amount By ID
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($entityId)
    {
        $dataModel = $this->getById($entityId);
        $this->delete($dataModel);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function saveBid($customerId, \Lof\Auction\Api\Data\AmountInterface $amount)
    {
        $dataModel = $this->amountFactory->create();
        $amount->setCustomerId($customerId);
        if ($dataModel->getEntityId()) {
            $dataModel->load((int)$dataModel->getEntityId());
        }
        try {
            $dataModel->setData((array)$amount->getData())->save();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the auction amount: %1',
                $exception->getMessage()
            ));
        }
        return $dataModel;
    }

    /**
     * @inheritdoc
     */
    public function placeManualBid($customerId, \Lof\Auction\Api\Data\AmountInterface $amount)
    {
        $bidAmount = $amount->getAuctionAmount();
        $productId = $amount->getProductId();
        if (!$bidAmount || !$productId) {
            throw new CouldNotSaveException(__(
                'Could not place manual bid for the auction. Missing required field auction_amount or product_id. Please try again'
            ));
        }
        $bidData = $this->placeBid($customerId, (int)$productId, (float)$bidAmount, false);
        if (isset($bidData['biddingId']) && $bidData['biddingId']) {
            return $this->getById($bidData['biddingId']);
        } else {
            throw new CouldNotSaveException(__(
                'Could not place manual bid for the auction.'
            ));
        }
    }

    /**
     * @inheritdoc
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
     * {@inheritdoc}
     */
    public function placeBid($customerId, $productId, $amount, $isAuto)
    {
        $auctionCollection = $this->auctionFactory->create()->getCollection();
        $auction = $auctionCollection->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS)
            ->getFirstItem();
        if ($auction->getData()) {
            $auctionData = $auction->getData();
            $auctionId = $auction->getEntityId();
            $auctionConfig = $this->helper->getAuctionConfiguration();
            $rate = $this->helper->getRates();
            $isAuto = $auctionConfig['auto_enable']
                && $auctionData['auto_auction_opt']
                && $isAuto;

            if (!$isAuto) {
                $amount = $amount/$rate;
            } else {
                $amount = $this->helper->getMinAmount($auctionId, $auctionConfig);
            }

            if ($this->helper->getConfig("general_settings/check_max_bids") && isset($auctionData['max_bids']) && $auctionData['max_bids']) {
                $totalBids = $this->helper->getTotalBids((int)$auctionData['entity_id'], $customerId);
                if ($totalBids > (int)$auctionData['max_bids']) {
                    return ['code' => 1, 'biddingId' => 0, 'message' => __('This auction is limit bid. You can not bid the auction at time.')];
                }
            }

            if ($this->getIsCheckContinues()) {
                $checkContinues = $this->helper->checkContinues($isAuto, $auctionData, $auctionConfig);
                if (!$checkContinues) {
                    return ['code' => 1, 'biddingId' => 0, 'message' => __('You have bid too much, wait a few minutes and try again.')];
                }
            }

            $checkAmount = $this->helper->checkAmount($amount, $auctionId, $auctionConfig);

            if (!$checkAmount) {
                return ['code' => 1, 'biddingId' => 0, 'message' => __('You can not bid greater than max price or lower than current price.')];
            }
            if ($isAuto) {
                return $this->saveAutoBiddingAmount($auctionData, $customerId, $amount, $auctionConfig);
            } else {
                return $this->saveBiddingAmount($auctionData, $customerId, $amount, $auctionConfig);
            }
        } else {
            return ['code' => 1, 'biddingId' => 0, 'message' => __('This product is not currently auctioned.')];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveAutoBiddingAmount($auctionData, $customerId, $amount, $auctionConfig)
    {
        $biddingId = 0;
        $biddingModel = $this->auctionFactory->create()->load($auctionData['entity_id']);
        $autoBidRecord = $this->autoAmountFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('auction_id', $auctionData['entity_id'])
            ->addFieldToFilter('status', '1')
            ->getFirstItem();
        if ($autoBidRecord->getData()) {
            if (!$auctionConfig['auto_auc_limit']) {
                return ['code' => '0', 'biddingId' => 0, 'message'=> __('You are not allowed to auto bid again.')];
            } else {
                $autoBidRecord->setId($autoBidRecord->getEntityId());
                $autoBidRecord->setAmount($amount);
                $autoBidRecord->setIsSpam(0);
                $autoBidRecord->setCreatedAt($this->_dateTime->date('Y-m-d H:i:s'));
                $autoBidRecord->save();
                $biddingId = $autoBidRecord->getEntityId();
            }
        } else {
            $autoBidModel = $this->autoAmountFactory->create();
            $autoBidModel->setAmount($amount);
            $autoBidModel->setCustomerId($customerId);
            $autoBidModel->setProductId($auctionData['product_id']);
            $autoBidModel->setAuctionId($auctionData['entity_id']);
            $autoBidModel->setStatus(1);
            $autoBidModel->setIsSpam(0);
            $autoBidModel->save();
            $biddingId = $autoBidModel->getEntityId();
        }

        if (!$autoBidRecord->getData() && $autoBidModel) {
            $autoBidRecord = $autoBidModel;
        }
        if (!$this->_isNotSaveHistory) {
            $this->_historyHelper->saveHistory($autoBidRecord);
        }

        if ($this->getAllowSendEmail()) {
            $this->sendAutoNotice($auctionData, $auctionConfig, $customerId, $amount);
            if ($auctionConfig['enable_outbid_email']) {
                $listForSendMail = $this->mixCollectionFactory->create()
                    ->addFieldToFilter('product_id', $auctionData['product_id'])
                    ->addFieldToFilter('auction_id', $auctionData['entity_id']);

                foreach ($listForSendMail as $key) {
                    $userId = $key->getCustomerId();
                    if ($customerId != $userId && $customerId != $userId) {
                        $this->helperEmail->sendMailToMembers($userId, $customerId, $auctionData['product_id']);
                    }
                }
            }
            if ($auctionConfig['enable_admin_email']) {
                $this->helperEmail->sendAutoMailToAdmin($customerId, $auctionData['product_id'], $amount);
            }
        }
        $biddingModel->setMinAmount($amount);
        $biddingModel->setCustomerId($customerId);
        $biddingModel->save();
        $this->_processAutoPlaceBid($biddingId, true);

        return ['code' => 0, 'biddingId' => $biddingId, 'message' => __('Your auto bid amount successfully saved')];
    }

    /**
     * {@inheritdoc}
     */
    public function saveBiddingAmount($auctionData, $customerId, $amount, $auctionConfig)
    {
        $biddingId = 0;
        $biddingModel = $this->auctionFactory->create()->load($auctionData['entity_id']);
        $bidCusrRecord = $this->amountFactory->create()->getCollection()
            ->addFieldToFilter('product_id', $auctionData['product_id'])
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('auction_id', $auctionData['entity_id'])
            ->addFieldToFilter('status', '1')
            ->getFirstItem();
        if ($bidCusrRecord->getEntityId()) {
            $bidCusrRecord->setId($bidCusrRecord->getEntityId());
            $bidCusrRecord->setAuctionAmount($amount);
            $bidCusrRecord->setIsSpam(0);
            $bidCusrRecord->save();
            $biddingId = $bidCusrRecord->getEntityId();
        } else {
            $bidModel = $this->amountFactory->create();
            $bidModel->setAuctionAmount($amount);
            $bidModel->setCustomerId($customerId);
            $bidModel->setProductId($auctionData['product_id']);
            $bidModel->setAuctionId($auctionData['entity_id']);
            $bidModel->setStatus(1);
            $bidModel->setIsSpam(0);
            $bidModel->save();
            $biddingId = $bidModel->getEntityId();
        }
        if (!$bidCusrRecord->getData() && $bidModel) {
            $bidCusrRecord = $bidModel;
        }

        if (!$this->_isNotSaveHistory) {
            $this->_historyHelper->saveHistory($bidCusrRecord);
        }

        if ($this->getAllowSendEmail()) {
            if ($auctionConfig['enable_admin_email']) {
                $this->helperEmail->sendMailToAdmin($customerId, $auctionData['product_id'], $amount);
            }
            if ($auctionConfig['enable_confirm_email']) {
                $this->helperEmail->sendMailToBidder($customerId, $auctionData['product_id'], $amount);
            }
            $this->sendAutoNotice($auctionData, $auctionConfig, $customerId, $amount);
            if ($auctionConfig['enable_outbid_email']) {
                $listForSendMail = $this->mixCollectionFactory->create()
                    ->addFieldToFilter('product_id', $auctionData['product_id'])
                    ->addFieldToFilter('auction_id', $auctionData['entity_id']);

                foreach ($listForSendMail as $key) {
                    $userId = $key->getCustomerId();
                    if ($customerId != $userId && $customerId != $userId) {
                        $this->helperEmail->sendMailToMembers($userId, $customerId, $auctionData['product_id']);
                    }
                }
            }
        }
        $biddingModel->setMinAmount($amount);
        $biddingModel->setCustomerId($customerId);
        $biddingModel->save();
        $this->_processAutoPlaceBid($biddingId, false);
        return ['code' => 0, 'biddingId' => $biddingId, 'message' => __('Your bid amount successfully saved.')];
    }

    /**
     * @param $auctionData
     * @param $auctionConfig
     * @param $amount
     * @param $customerId
     */
    public function sendAutoNotice($auctionData, $auctionConfig, $amount, $customerId)
    {
        $listToSendMail = $this->autoAmountFactory->create()->getCollection()
            ->addFieldToFilter('auction_id', $auctionData['entity_id'])
            ->addFieldToFilter('status', '1')
            ->setOrder('amount');

        $max = $listToSendMail->getFirstItem()->getAmount();

        if ($auctionConfig['enable_outbid_email'] && $max <= $amount) {
            foreach ($listToSendMail as $autoAmount) {
                if ($autoAmount->getAmount()<= $amount) {
                    if ($customerId != $autoAmount->getCustomerId()) {
                        $this->helperEmail->sendOutBidAutoBidder($customerId, $customerId, $auctionData['product_id']);
                    }
                }
            }
        }
    }

    /**
     * Set is check continues
     *
     * @param bool
     * @return $this
     */
    public function setIsCheckContinues($flag = true)
    {
        $this->_isCheckContinues = $flag;
        return $this;
    }

    /**
     * Set Is allow callback
     *
     * @param bool
     * @return $this
     */
    public function setIsAllowCallback($flag = true)
    {
        $this->_isAllowCallback = $flag;
        return $this;
    }

    /**
     * Get is check continues
     *
     * @return bool|int
     */
    public function getIsCheckContinues()
    {
        return $this->_isCheckContinues;
    }

    /**
     * Set is check _isAllowSendEmail
     *
     * @param bool
     * @return $this
     */
    public function setAllowSendEmail($flag = true)
    {
        $this->_isAllowSendEmail = $flag;
        return $this;
    }

    /**
     * Set is check _isNotSaveHistory
     *
     * @param bool
     * @return $this
     */
    public function setIsNotSaveHistory($flag = true)
    {
        $this->_isNotSaveHistory = $flag;
        return $this;
    }

    /**
     * Set callback object
     * @param Object
     * @return $this
     */
    public function setCallBackCreateAutoBidObject($object)
    {
        $this->_callbackObject = $object;
        return $this;
    }

    /**
     * Get is check _isAllowSendEmail
     *
     * @return bool|int
     */
    public function getAllowSendEmail()
    {
        return $this->_isAllowSendEmail;
    }

    /**
     * Get Manual Amount Bid Collection
     *
     * @return
     */
    public function getAmountCollection()
    {
        return $this->modelCollectionFactory->create();
    }

    /**
     * Get Auto Bid Collection
     *
     * @return
     */
    public function getAutoCollection()
    {
        return $this->autoAmountFactory->create()->getCollection();
    }

    /**
     * process auto bid
     *
     * @param int $biddingId
     * @param bool $isAuto
     * @return bool|int|mixed|null
     */
    protected function _processAutoPlaceBid($biddingId, $isAuto = true)
    {
        //Call rest api auto create bid
        return $biddingId && $this->_callbackObject && $this->_isAllowCallback ? $this->_callbackObject->createAutoBid($biddingId, $isAuto) : false;
    }
}
