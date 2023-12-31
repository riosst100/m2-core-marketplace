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

namespace Lof\Auction\Controller\Account;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\Email;
use Lof\Auction\Helper\History as HistoryHelper;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\HistoryFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Api\MaxAbsenteeBidRepositoryInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Login
 * Lof\Auction\Controller\Account
 */
class Login extends AbstractAccount
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $_dirCurrencyFactory;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timeZone;

    /**
     * @var AmountFactory
     */
    protected $_auctionAmount;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var AutoAuctionFactory
     */
    protected $_autoAuction;
    /**
     * @var Email
     */
    private $_helperEmail;
    /**
     * @var HistoryFactory
     */
    private $_historyFactory;
    /**
     * @var MixAmountCollectionFactory
     */
    private $_mixAmountCollectionFactory;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $_priceHelper;

    /**
     * @var HistoryHelper $_historyFactory
     */
    private $_historyHelper;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var MaxAbsenteeBidRepositoryInterface
     */
    protected $_maxAbsenteeBidRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $dirCurrencyFactory
     * @param DateTime $dateTime
     * @param TimezoneInterface $timeZone
     * @param AmountFactory $auctionAmount
     * @param ProductFactory $auctionProductFactory
     * @param Data $helperData
     * @param Email $helperEmail
     * @param AutoAuctionFactory $autoAuction
     * @param HistoryFactory $historyFactory
     * @param MixAmountCollectionFactory $mixAmountCollectionFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param HistoryHelper $historyHelper
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param MaxAbsenteeBidRepositoryInterface $maxAbsenteeBidRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CurrencyFactory $dirCurrencyFactory,
        DateTime $dateTime,
        TimezoneInterface $timeZone,
        AmountFactory $auctionAmount,
        ProductFactory $auctionProductFactory,
        Data $helperData,
        Email $helperEmail,
        AutoAuctionFactory $autoAuction,
        HistoryFactory $historyFactory,
        MixAmountCollectionFactory $mixAmountCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        HistoryHelper $historyHelper,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        MaxAbsenteeBidRepositoryInterface $maxAbsenteeBidRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_dirCurrencyFactory = $dirCurrencyFactory;
        $this->_dateTime = $dateTime;
        $this->_timeZone = $timeZone;
        $this->_auctionAmount = $auctionAmount;
        $this->_auctionProductFactory = $auctionProductFactory;
        $this->_helperData = $helperData;
        $this->_helperEmail = $helperEmail;
        $this->_autoAuction = $autoAuction;
        $this->_historyFactory = $historyFactory;
        $this->_mixAmountCollectionFactory = $mixAmountCollectionFactory;
        $this->_priceHelper = $priceHelper;
        $this->_historyHelper = $historyHelper;
        $this->_cacheTypeList       = $cacheTypeList;
        $this->_maxAbsenteeBidRepository = $maxAbsenteeBidRepository;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            //set Data in session if bidder not login
            $this->_customerSession->setAuctionBidData($request->getParams());
        }
        return parent::dispatch($request);
    }

    /**
     * Default customer account page
     * @return Redirect $resultRedirect
     * @throws NoSuchEntityException
     * @throws Exception
     * @var $cuntCunyCode current Currency Code
     * @var $baseCunyCode base Currency Code
     */
    public function execute()
    {
        //get data from customerSession relared to Auction
        $data = $this->_customerSession->getAuctionBidData();
        $data = $data ? $data : $this->getRequest()->getParams();
        $auctionData = [];
        if (isset($data['data_auction']) && $data['data_auction']) {
            $auctionData = json_decode($data['data_auction'], true);
        }
        if (!$auctionData || count($auctionData) <=0) {
            if (isset($data['id']) && $data['id']) {
                $auctionData = $this->_helperData->getAuctionDetail($data['id']);
            } else {
                $auctionData = $this->_customerSession->getAuctionData();
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        if (!isset($auctionData['pro_url']) || $auctionData['pro_url'] == '') {
            $url_key = isset($auctionData['url_key']) && $auctionData['url_key'] ? ($auctionData['url_key'].'.html') : '';
            $auctionData['pro_url'] = $this->_url->getBaseUrl(). $url_key;
        }
        $url = $resultRedirect->setUrl($auctionData['pro_url']);
        //unset data of customerSession related to Auction
        $this->_customerSession->unsAuctionBidData();
        $this->_customerSession->unsAuctionData();

        $today = $this->_timeZone->date()->format('m/d/y H:i:s');

        if (isset($auctionData['stop_auction_time'])) {
            $difference = strtotime($auctionData['stop_auction_time']) - strtotime($today);
        } else {
            $difference = 0;
        }

        if ($data && $difference > 0) {
            $flag = true;
            if ($this->_helperData->getConfig("general_settings/check_max_bids") && isset($auctionData['max_bids']) && $auctionData['max_bids']) {
                $totalBids = $this->_helperData->getTotalBids((int)$auctionData['entity_id'], $this->_customerSession->getCustomerId());
                $flag = $totalBids > (int)$auctionData['max_bids'] ? false : true;
            }
            if ($flag) {
                //auction configuration detail
                $auctionConfig = $this->_helperData->getAuctionConfiguration();

                //get currency according to store
                $rate = $this->_helperData->getRates();

                $data['auction_id'] = $auctionData['entity_id'];
                $data['product_id'] = $auctionData['product_id'];
                $data['pro_name'] = $auctionData['pro_name'];
                $isAuto = $auctionConfig['auto_enable']
                    && $auctionData['auto_auction_opt']
                    && array_key_exists("auto_bid_allowed", $data);
                if (!$isAuto && !$this->validate($data)) {
                    return $url;
                }
                if (!$isAuto) {
                    $data['bidding_amount'] = $data['bidding_amount'] / $rate;
                } else {
                    $data['bidding_amount'] = $this->_helperData->getMinAmount($data['auction_id'], $auctionConfig);
                }
                if(!isset($auctionData["customer_id"]) || (isset($auctionData["customer_id"]) && !$auctionData["customer_id"])){
                    $auctionData["customer_id"] = $this->_customerSession->getCustomerId();
                }
                $checkContinues = $this->_helperData->checkContinues($isAuto, $auctionData, $auctionConfig);
                if (!$this->checkAmount($data['bidding_amount'], $data['auction_id'], $auctionConfig)) {
                    return $url;
                }
                //$checkContinues = true;
                if (!$checkContinues) {
                    $this->messageManager->addError(__('You have bid too much, wait a few minutes and try again.'));
                } else {
                    if ($isAuto) {
                        $this->_saveAutobiddingAmount($data);
                    } else {
                        $this->_saveBiddingAmount($data);
                    }
                    //$this->_cacheTypeList->cleanType('full_page');
                }
            } else {
                $this->messageManager->addError(__('This auction is limit bid. You can not bid the auction at time.'));
            }
        } else {
            $this->messageManager->addError(__('Auction time expired...'));
        }
        return $url;
    }

    /**
     * process auto bid
     *
     * @param int $biddingId
     * @param bool $isAuto
     * @return bool|int|mixed|null
     */
    protected function _processAutoPlaceBid($biddingId, $bidModel, $auctionId, $isAuto = true)
    {
        //Call rest api auto create bid
        if ($biddingId) {
            //Start: get available absentee collection
            $absenteeCollection = $this->_maxAbsenteeBidRepository->getAvailableMaxAbsenteeBid($auctionId, $bidModel->getCustomerId(), $bidModel->getAmount());
            if ($absenteeCollection) {
                //Process create auto bid for absentee list
                $this->_maxAbsenteeBidRepository->setMaxAbsenteeBidList($absenteeCollection)
                                                ->createAutoBid($biddingId, $isAuto);

                //End: process update max bid amount
                $this->_maxAbsenteeBidRepository->setMaxAbsenteeBidList($absenteeCollection)
                                                ->processUpdateBid($auctionId);
            }
            return true;
        }
        return false;
    }

    /**
     * _saveBiddingAmount saves amount of normal bid placed by customer
     * @param array $data stores bid data
     * @throws Exception
     * @var int $val bid amount
     * @var int $userId current logged in customer's id
     * @var object $biddingModel holds data for particular bid
     * @var $minPrice int stores minimum price of bidding
     * @var $incopt stores increament option is allowed on bidding or not
     * @var $incPrice holds increment price for product
     * @var $bidCusrRecord object id customer already placed a bid
     * @var $bidModel use to strore new bidding
     */
    protected function _saveBiddingAmount($data)
    {
        $biddingId = 0;
        $bidModel = null;
        $val = $data['bidding_amount'];
        $browser = $this->getRequest()->getServer('HTTP_USER_AGENT');
        $auctionConfig = $this->_helperData->getAuctionConfiguration();
        $userId = $this->_customerSession->getCustomerId();
        $biddingModel = $this->_auctionProductFactory->create()->load($data['auction_id']);
        $bidCusrRecord = $this->_auctionAmount->create()->getCollection()
            ->addFieldToFilter('product_id', $data['product_id'])
            ->addFieldToFilter('customer_id', $userId)
            ->addFieldToFilter('auction_id', $data['auction_id'])
            ->getFirstItem();
        if ($bidCusrRecord->getEntityId()) {
            $bidCusrRecord->setId($bidCusrRecord->getEntityId());
            $bidCusrRecord->setAuctionAmount($data['bidding_amount']);
            $bidCusrRecord->setIsSpam(0);
            $bidCusrRecord->save();
            $biddingId = $bidCusrRecord->getEntityId();
        } else {
            $bidModel = $this->_auctionAmount->create();
            $bidModel->setAuctionAmount($data['bidding_amount']);
            $bidModel->setCustomerId($userId);
            $bidModel->setProductId($data['product_id']);
            $bidModel->setAuctionId($data['auction_id']);
            $bidModel->setStatus(1);
            $bidModel->setIsSpam(0);
            $bidModel->save();
            $biddingId = $bidModel->getEntityId();
        }

        if (!$bidCusrRecord->getData() && $bidModel) {
            $bidCusrRecord = $bidModel;
        }
        $this->_historyHelper->saveHistory($bidCusrRecord, $browser);

        if ($auctionConfig['enable_admin_email']) {
            $this->_helperEmail->sendMailToAdmin($userId, $data['product_id'], $data['bidding_amount']);
        }
        if ($auctionConfig['enable_confirm_email']) {
            $this->_helperEmail->sendMailToBidder($userId, $data['product_id'], $data['bidding_amount']);
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
        $biddingModel->setMinAmount($val);
        $biddingModel->setCustomerId($userId);
        $biddingModel->save();
        $this->messageManager->addSuccess(__('Your bid amount successfully saved.'));

        $this->_processAutoPlaceBid($biddingId, $bidCusrRecord, $biddingModel->getId(),  false);
    }

    /**
     * _saveAutobiddingAmount calls to store auto bid placed by customer
     * @param array $data holda data related to bidding
     * @throws Exception
     * @var $userId int holds current customer id
     * @var $biddingModel object stores bidding details
     * @var $auctionConfig ['auto_use_increment'] int stores whether increment option is enable in admin panel or not
     * @var $auctionConfig ['auto_auc_limit'] stores whether customer can place auto bid multiple times or not
     * @var $minPrice int stores minimum price of bidding
     * @var $incopt stores increament option is allowed on bidding or not
     * @var $incprice holds increment price for product
     * @var $pid int product id on which bid is placed
     * @var $val int bidding amount placed by customer
     * @var $autoBidRecord object checks whether there is already bid already exist for current customer or not
     * @var $autoBidModel autobid model to store auto bid
     * @var $listToSendMail use to get maximum auto bid amount
     */

    protected function _saveAutobiddingAmount($data)
    {
        $biddingId = 0;
        $autoBidModel = null;
        $browser = $this->getRequest()->getServer('HTTP_USER_AGENT');
        $val = $data['bidding_amount'];
        $userId = $this->_customerSession->getCustomerId();
        $auctionConfig = $this->_helperData->getAuctionConfiguration();
        $biddingModel = $this->_auctionProductFactory->create()->load($data['auction_id']);
        $autoBidRecord = $this->_autoAuction->create()->getCollection()
            ->addFieldToFilter('customer_id', $userId)
            ->addFieldToFilter('auction_id', $data['auction_id'])
            ->addFieldToFilter('status', '1')
            ->getFirstItem();
        if ($autoBidRecord->getEntityId()) {
            if (!$auctionConfig['auto_auc_limit']) {
                $this->messageManager->addError(__('You are not allowed to auto bid again.'));
                return;
            } else {
                $autoBidRecord->setId($autoBidRecord->getEntityId());
                $autoBidRecord->setAmount($val);
                $autoBidRecord->setIsSpam(0);
                $autoBidRecord->setCreatedAt($this->_dateTime->date('Y-m-d H:i:s'));
                $autoBidRecord->save();
                $biddingId = $autoBidRecord->getEntityId();
            }
        } else {
            $autoBidModel = $this->_autoAuction->create();
            $autoBidModel->setAmount($val);
            $autoBidModel->setCustomerId($userId);
            $autoBidModel->setProductId($data['product_id']);
            $autoBidModel->setAuctionId($data['auction_id']);
            $autoBidModel->setStatus(1);
            $autoBidModel->setIsSpam(0);
            $autoBidModel->save();

            $biddingId = $autoBidModel->getEntityId();
        }

        if (!$autoBidRecord->getData() && $autoBidModel) {
            $autoBidRecord = $autoBidModel;
        }

        $this->_historyHelper->saveHistory($autoBidRecord, $browser);

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
            $this->_helperEmail->sendAutoMailToAdmin($userId, $data['product_id'], $data['bidding_amount']);
        }

        $biddingModel->setMinAmount($val);
        $biddingModel->setCustomerId($userId);
        $biddingModel->save();
        $this->messageManager->addSuccess(__('Your auto bid amount successfully saved'));
        $this->_processAutoPlaceBid($biddingId, $autoBidRecord, $biddingModel->getId(),  true);
    }

    /**
     * @param $amount
     * @param $auctionId
     * @param $auctionConfig
     * @return bool
     */
    public function checkAmount($amount, $auctionId, $auctionConfig)
    {
        $val = (float)$amount;
        $biddingModel = $this->_auctionProductFactory->create()->load($auctionId);
        $maxPrice = (float)$biddingModel->getMaxAmount();
        if ($auctionConfig['increment_auc_enable'] && $biddingModel->getIncrementOpt()) {
            $step = $this->_helperData->getIncrementPriceAsRange($biddingModel->getMinAmount());
            if ($step) {
                $currentStep = $amount - $biddingModel->getMinAmount();
                if (fmod($currentStep, $step) != 0) {
                    $this->messageManager->addErrorMessage(__('The added price must be a multiple of %1', $step));
                    return false;
                }
            }
        }
        if ($maxPrice && $maxPrice < $val) {
            $this->messageManager->addErrorMessage(__('You can not bid greater than max price.'));
            return false;
        } else {
            $minAmount = $this->_helperData->getMinAmount($auctionId, $auctionConfig);
            //$fixVal = $val+0.0001;
            //if($minAmount > $val && $minAmount <= $fixVal){
            //    return true;
            //}
            $minAmount = !empty($minAmount) ? floatval(filter_var($minAmount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) : 0;
            if ($minAmount > $val && $minAmount !== $val) {
                $this->messageManager->addErrorMessage(__('You can not bid less than or equal to current price.'));
                return false;
            }
        }
        return true;
    }

    /**
     * @param $data
     * @param $auctionConfig
     * @param $val
     * @param $userId
     */
    public function sendOutBid($data, $auctionConfig, $val, $userId)
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

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        if (isset($data['bidding_amount']) && $data['bidding_amount']) {
            if (!is_numeric($data['bidding_amount'])) {
                $this->messageManager->addErrorMessage(__('The amount entered must be a number.'));
                return false;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Please enter the price you want to place bid.'));
            return false;
        }
        return true;
    }
}
