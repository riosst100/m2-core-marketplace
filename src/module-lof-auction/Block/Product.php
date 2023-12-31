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
namespace Lof\Auction\Block;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\Collection;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\Serializer\Json;
use \Magento\Framework\App\ObjectManager;

/**
 * Class Product
 * @package Lof\Auction\Block
 */
class Product extends Template
{
    /**
     * @var Registry
     */
    private $_coreRegistry;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $_product;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var PriceCurrencyInterface
     */
    private $_priceHelper;

    /**
     * @var ProductFactory
     */
    private $_auctionProFactory;

    /**
     * @var AmountFactory
     */
    private $_aucAmountFactory;

    /**
     * @var AutoAuctionFactory
     */
    private $_autoAuctionFactory;

    /**
     * @var Data
     */
    private $_helperData;

    /**
     * @var TimezoneInterface
     */
    private $_timezone;

    /**
     * @var DateTime
     */
    private $_dateTime;

    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * @var MixAmountCollectionFactory
     */
    private $_mixAmountCollectionFactory;

    /**
     * Json Serializer Instance
     *
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\Product $product
     * @param CustomerSession $customerSession
     * @param PriceCurrencyInterface $priceHelper
     * @param ProductFactory $auctionProFactory
     * @param AmountFactory $aucAmountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param Data $helperData
     * @param TimezoneInterface $timezone
     * @param DateTime $dateTime
     * @param MixAmountCollectionFactory $mixAmountCollectionFactory
     * @param array $data
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\Product $product,
        CustomerSession $customerSession,
        PriceCurrencyInterface $priceHelper,
        ProductFactory $auctionProFactory,
        AmountFactory $aucAmountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        Data $helperData,
        TimezoneInterface $timezone,
        DateTime $dateTime,
        MixAmountCollectionFactory $mixAmountCollectionFactory,
        array $data = [],
        Json $json = null
    ) {
        $this->_coreRegistry = $context->getRegistry();
        $this->_product = $product;
        $this->_customerSession = $customerSession;
        $this->_priceHelper = $priceHelper;
        $this->_auctionProFactory = $auctionProFactory;
        $this->_aucAmountFactory = $aucAmountFactory;
        $this->_autoAuctionFactory = $autoAuctionFactory;
        $this->_helperData = $helperData;
        $this->_timezone = $timezone;
        $this->_dateTime = $dateTime;
        $this->_mixAmountCollectionFactory = $mixAmountCollectionFactory;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);

        parent::__construct($context, $data);

        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [\Lof\Auction\Model\Product::CACHE_TAG]]);
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        return $this->json->serialize($this->jsLayout);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = $this->getProductId();
        $currentCustomerId = "guest";
        if ($this->_customerSession->isLoggedIn()) {
            $currentCustomerId = $this->_customerSession->getCustomerId();
        }
        $conditions .= ".".$currentCustomerId;
        return [
            'LOF_AUCTION_PRODUCT_BLOCK',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            'template' => $this->getTemplate(),
            $conditions,
            $this->json->serialize($this->getRequest()->getParams())
        ];
    }

    /**
     * Returns popup config in JSON format.
     *
     * Added in scope of https://github.com/magento/magento2/pull/8617
     * @param mixed|array $data
     * @return bool|string
     * @since 101.0.0
     */
    public function getSerializedConfig($data)
    {
        return $this->json->serialize($data);
    }

    /**
     * @param $auctionId
     * @return Collection
     */
    public function getHistory($auctionId)
    {
        return $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', ['eq' => $auctionId])
            ->addFieldToFilter('is_spam', ['eq' => 0])
            ->setOrder('amount', 'DESC');
    }

    /**
     *
     * @param $customerId
     * @param int $currentProId
     * @return DataObject of current category product in auction and buy it now
     */
    public function getHistoryAuto($customerId, $currentProId = 0)
    {
        if (!$currentProId) {
            $curPro = $this->_coreRegistry->registry('current_product');
            if ($curPro) {
                $currentProId = $curPro->getEntityId();
            } else {
                $auctionId = $this->getRequest()->getParam('id');
                $currentProId = $this->getAuctionProId($auctionId);
                $curPro = $this->_product->load($currentProId);
            }
        }
        return $this->_autoAuctionFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $currentProId])
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS])
            ->getFirstItem();
    }

    /**
     * @return mixed|array of current category product in auction and buy it now
     * @throws Exception
     * @var $productList Product list of current page
     */
    public function getAuctionDetail()
    {
        if (!$this->getData("auction_detail")) {
            $auctionData = $this->_coreRegistry->registry('auction_detail');
            if (empty($auctionData)) {
                $auctionConfig = $this->getAuctionConfiguration();
                $auctionData = false;
                if ($auctionConfig['enable']) {
                    $curPro = $this->_coreRegistry->registry('current_product');

                    if ($curPro) {
                        $currentProId = $curPro->getEntityId();
                    } else {
                        $auctionId = $this->getRequest()->getParam('id');
                        $currentProId = $this->getAuctionProId($auctionId);
                        $curPro = $this->_product->load($currentProId);
                    }

                    $auctionData = $this->_auctionProFactory->create()->getCollection()
                        ->addFieldToFilter('product_id', ['eq' => $currentProId])
                        ->addFieldToFilter('auction_status',
                        [
                            'in' => [
                                AuctionStatus::STATUS_TIME_END,
                                AuctionStatus::STATUS_PROCESS,
                                AuctionStatus::STATUS_WINNER_NOT_DEFINED
                            ]
                        ])
                        ->setOrder('entity_id')
                        ->getFirstItem()
                        ->getData();

                    if (isset($auctionData['entity_id'])) {
                        $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
                            ->addFieldToFilter('product_id', ['eq' => $currentProId])
                            ->addFieldToFilter('auction_id', ['eq'=> $auctionData['entity_id']])
                            ->addFieldToFilter('winning_status', ['eq'=>1])
                            ->addFieldToFilter('shop', ['neq'=>0])
                            ->getFirstItem();

                        if ($aucAmtData->getEntityId()) {
                            $aucAmtData = $this->_autoAuctionFactory->create()->getCollection()
                                ->addFieldToFilter('auction_id', ['eq'=> $auctionData['entity_id']])
                                ->addFieldToFilter('flag', ['eq'=>1])
                                ->addFieldToFilter('shop', ['neq'=>0])
                                ->getFirstItem();
                        }
                        $auctionData['time_zone'] = $this->_helperData->getTimezoneName();
                        $auctionData['stop_auction_utc_time'] = $auctionData['stop_auction_time'];
                        $auctionData['start_auction_utc_time'] = $auctionData['start_auction_time'];
                        //$today = $this->_localeDate->date()->format('m/d/y H:i:s');
                        $today = $this->_helperData->getTimezoneDateTime();
                        $auctionData['stop_auction_time'] = $this->_helperData->getTimezoneDateTime($auctionData['stop_auction_time']);
                        $auctionData['start_auction_time'] = $this->_helperData->getTimezoneDateTime($auctionData['start_auction_time']);
                        $auctionData['today_time'] = $today;
                        $auctionData['current_time_stamp'] = strtotime($today);
                        $auctionData['start_auction_time_stamp'] = strtotime($auctionData['start_auction_time']);
                        $auctionData['stop_auction_time_stamp'] = strtotime($auctionData['stop_auction_time']);
                        $auctionData['new_auction_start'] = $aucAmtData->getEntityId() ? true : false;
                        $auctionData['auction_title'] = __('Bid on ').$curPro->getName();
                        // $auctionData['pro_url'] = $curPro->getProductUrl();
                        $auctionData['pro_url'] = $this->getUrl().$curPro->getUrlKey().'.html';
                        $auctionData['pro_name'] = $curPro->getName();
                        $auctionData['sku'] = $curPro->getSku();
                        $auctionData['max_bids'] = isset($auctionData['max_bids']) ? (int)$auctionData['max_bids'] : 0;
                        $auctionData['pro_buy_it_now'] = $auctionData['buy_it_now'];
                        $auctionData['buy_it_now'] = $auctionData['buy_it_now'];
                        $auctionData['pro_auction'] = $auctionData['auction_status'];
                        if ($auctionData['min_amount'] < $auctionData['starting_price']) {
                            $auctionData['min_amount'] = $auctionData['starting_price'];
                        }
                    } else {
                        $auctionData = false;
                    }
                }
                $this->_coreRegistry->register('auction_detail', $auctionData);
            }
            $this->_customerSession->setAuctionData($auctionData);
            $this->setAuctionDetail($auctionData);
        }
        return $this->getData("auction_detail");
    }

    /**
     * set auction detail
     *
     * @param mixed|array $auctionDetail
     * @return $this
     */
    public function setAuctionDetail($auctionDetail)
    {
        $this->setData("auction_detail", $auctionDetail);
        return $this;
    }

    /**
     * @return array of Auction Configuration Settings
     */
    public function getAuctionConfiguration()
    {
        return $this->_helperData->getAuctionConfiguration();
    }

    /**
     * @param $auctionId
     * @return string url of auction form
     */

    public function getAuctionFormAction($auctionId)
    {
        return $this->_customerSession->isLoggedIn() ?
            $this->_urlBuilder->getUrl("lofauction/account/login"):
            $this->_urlBuilder->getUrl('lofauction/account/login/', ['id'=>$auctionId]);
    }

    /**
     * @param $auctionData
     * @return array[]|bool[]
     */
    public function getAuctionDetailAfterEnd($auctionData)
    {
        $currentProId = $auctionData['product_id'];
        $auctionId = $auctionData['entity_id'];

        $customerId = 0;
        $shop = '';
        $price = 0;

        $winnerBidDetail = $this->_helperData->getWinnerBidDetail($auctionId);

        if ($winnerBidDetail) {
            $customerId = $winnerBidDetail->getCustomerId();
            $shop = $winnerBidDetail->getShop();
            $price = $winnerBidDetail->getBidType() == 'normal' ? $winnerBidDetail->getAuctionAmount():
                $winnerBidDetail->getWinningPrice();
        }

        $waittingList = $this->_aucAmountFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq'=>$currentProId])
            ->addFieldToFilter('auction_id', ['eq'=>$auctionId])
            ->addFieldToFilter('winning_status', ['neq'=>1])
            ->addFieldToFilter('status', ['eq'=>AuctionStatus::STATUS_TIME_END]);

        $autoWaittingList = $this->_autoAuctionFactory->create()->getCollection()
            ->addFieldToFilter('auction_id', ['eq'=> $auctionId])
            ->addFieldToFilter('flag', ['neq'=>1])
            ->addFieldToFilter('status', ['eq'=>AuctionStatus::STATUS_TIME_END]);
        $custList=[];

        //get watting winner list from auction amount
        foreach ($waittingList as $waitAuc) {
            if ($waitAuc->getCustomerId()!= $customerId && !in_array($waitAuc->getCustomerId(), $custList)) {
                array_push($custList, $waitAuc->getCustomerId());
            }
        }
        //get watting winner list from auto auction
        foreach ($autoWaittingList as $autoWaitAuc) {
            if ($autoWaitAuc->getBuyerId()!= $customerId && !in_array($autoWaitAuc->getBuyerId(), $custList)) {
                array_push($custList, $autoWaitAuc->getBuyerId());
            }
        }

        $currentUserWinner = false;
        $currentUserWaitingList = false;
        if ($this->_customerSession->isLoggedIn()) {
            $currentCustomerId = $this->_customerSession->getCustomerId();
            if ($currentCustomerId == $customerId) {
                $day = strtotime($auctionData['stop_auction_time']. ' + '.$auctionData['days'].' days');
                $difference = $day - $auctionData['current_time_stamp'];
                $currentUserWinner = ['shop' => $shop, 'price' => $price, 'time_for_buy' => $difference];
            }

            if (in_array($currentCustomerId, $custList)) {
                $currentUserWaitingList = [
                    'auc_list_url' => $this->_urlBuilder->getUrl('auction/account/history', ['id'=>$auctionId]),
                    'auc_url_lable' => count($waittingList).__(' Bids'),
                    'msg_lable' => $customerId ? __('Bidding has been done for this product.'):
                        __('Bidding has been done for this product.'),
                ];
            }
        }
        return ['watting_user'=> $currentUserWaitingList, 'winner' => $currentUserWinner ];
    }

    /**
     * For get Bidding history link
     * @param array $auctionDetail
     * @return string url
     */
    public function getHistoryUrl($auctionData)
    {
        return $this->_urlBuilder->getUrl('lofauction/account/history', ['id'=>$auctionData['entity_id']]);
    }

    /**
     * For get Bidding record count
     * @param int $auctionId
     * @return string
     */
    public function getNumberOfBid($auctionId)
    {
        $records = $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', ['eq'=>$auctionId])
            ->addFieldToFilter('is_spam', 0);
        if (count($records) < 2) {
            return count($records) . __(' Bid');
        } else {
            return count($records) . __(' Bids');
        }
    }

    /**
     * get currency in format
     * @param $amount float
     * @return string
     *
     */
    public function formatPrice($amount)
    {
        return $this->_priceHelper->convertAndFormat($amount);
    }

    /**
     * get auction product id
     * @param $auctionId int
     * @return int
     *
     */
    public function getAuctionProId($auctionId)
    {
        return  $this->_auctionProFactory->create()->load($auctionId)->getProductId();
    }

    /**
     * getProAuctionType
     * @return string type of auction
     */
    public function getProAuctionType()
    {
        $curPro = $this->_coreRegistry->registry('current_product');
        $auctionType = "";
        if ($curPro) {
            $auctionOpt = explode(',', $curPro->getAuctionType());
            if ((in_array(2, $auctionOpt) && in_array(1, $auctionOpt)) || in_array(1, $auctionOpt)) {
                $auctionType = 'buy-it-now';
            } elseif (in_array(2, $auctionOpt)) {
                $auctionType = 'auction';
            }
        }
        return $auctionType;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getTimeZone()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        return $this->_timezone->getConfigTimezone(ScopeInterface::SCOPE_STORES, $storeId);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $template = $this->getTemplate();
        if (!$template) {
            $this->setTemplate('Lof_Auction::product.phtml');
        }
        $auction = $this->getAuctionData();
        if ($auction && $auction->getId()) {
            return parent::_toHtml(); // TODO: Change the autogenerated stub
        } else {
            return '';
        }
    }

    /**
     * get auction detail block
     *
     * @param string $blockName
     * @param mixed $auctionData
     * @return string
     */
    public function getAuctiondDetailBlock($blockName = 'auction.detail', $auctionData = null)
    {
        $auctionDetail = $this->getLayout()->createBlock(
            \Lof\Auction\Block\AuctionDetail::class,
            $blockName,
            ['data' => ['template' => 'Lof_Auction::auction-info.phtml']]
        );
        $auctionDetail->setAuctionDetail($auctionData);
        return $auctionDetail->toHtml();
    }

    /**
     * get auction info
     *
     * @return \Lof\Auction\Model\Product|null
     */
    public function getAuctionData()
    {
        if (!$this->getData("auction_data")) {
            $auction = $this->_coreRegistry->registry('auction_data');
            if (empty($auction)) {
                $productId = $this->getCurrentProduct()->getId();
                $auction = $this->_auctionProFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('product_id', $productId)
                            ->getFirstItem();
                $this->_coreRegistry->register('auction_data', $auction);
            }
            $this->setAuctionData($auction);
        }
        return $this->getData("auction_data");
    }

    /**
     * set auction info
     * @param mixed $auction
     * @return $this
     */
    public function setAuctionData($auction)
    {
        $this->setData("auction_data", $auction);
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        $url = $this->_urlBuilder->getCurrentUrl();
        return $url;
    }

    /**
     * get current product id
     *
     * @return int
     */
    public function getProductId()
    {
        $product = $this->getCurrentProduct();
        return $product ? (int)$product->getId() : 0;
    }
}
