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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\Auction\Block;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\ProductFactory;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Product
 * @package Lofmp\Auction\Block
 */
class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
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
     * Product constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\Product $product
     * @param CustomerSession $customerSession
     * @param PriceCurrencyInterface $priceHelper
     * @param ProductFactory $auctionProFactory
     * @param AmountFactory $aucAmountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param Data $helperData
     * @param array $data
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
        array $data = []
    ) {
        $this->_coreRegistry = $context->getRegistry();
        $this->_product = $product;
        $this->_customerSession = $customerSession;
        $this->_priceHelper = $priceHelper;
        $this->_auctionProFactory = $auctionProFactory;
        $this->_aucAmountFactory = $aucAmountFactory;
        $this->_autoAuctionFactory = $autoAuctionFactory;
        $this->_helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * @var $productList Product list of current page
     * @return array of current category product in auction and buy it now
     */
    public function getHistory()
    {
        $curPro = $this->_coreRegistry->registry('current_product');
        if ($curPro) {
            $currentProId = $curPro->getEntityId();
        } else {
            $auctionId = $this->getRequest()->getParam('id');
            $currentProId = $this->getAuctionProId($auctionId);
            $curPro = $this->_product->load($currentProId);
        }
        $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $currentProId])
            ->addFieldToFilter('status', ['eq'=>1])
            ->setOrder('auction_amount', 'DESC');
        return  $aucAmtData;
    }
    /**
     * @var $productList Product list of current page
     * @return array of current category product in auction and buy it now
     */
    public function getHistoryAuto($customerId)
    {
        $curPro = $this->_coreRegistry->registry('current_product');
        if ($curPro) {
            $currentProId = $curPro->getEntityId();
        } else {
            $auctionId = $this->getRequest()->getParam('id');
            $currentProId = $this->getAuctionProId($auctionId);
            $curPro = $this->_product->load($currentProId);
        }
        $aucAmtData = $this->_autoAuctionFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $currentProId])
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', ['eq'=>1])
            ->getFirstItem();
        return  $aucAmtData;
    }
    /**
     * @var $productList Product list of current page
     * @return array of current category product in auction and buy it now
     */
    public function getAuctionDetail()
    {
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
                                    ->addFieldToFilter('product_id', ['eq'=>$currentProId])
                                    ->addFieldToFilter('auction_status', ['in' => [0,1]])
                                    ->addFieldToFilter('status', ['eq'=>0])->getFirstItem()->getData();

            if (isset($auctionData['entity_id'])) {
                if ($auctionData['increment_opt'] && $auctionConfig['increment_auc_enable']) {
                    $incVal = $this->_helperData->getIncrementPriceAsRange($auctionData['min_amount']);
                    $auctionData['min_amount'] = $incVal ? $auctionData['min_amount'] + $incVal
                                                                        : $auctionData['min_amount'];
                }


                $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
                                        ->addFieldToFilter('product_id', ['eq' => $currentProId])
                                        ->addFieldToFilter('auction_id', ['eq'=> $auctionData['entity_id']])
                                        ->addFieldToFilter('winning_status', ['eq'=>1])
                                        ->addFieldToFilter('shop', ['neq'=>0])->getFirstItem();

                if ($aucAmtData->getEntityId()) {
                    $aucAmtData = $this->_autoAuctionFactory->create()->getCollection()
                                            ->addFieldToFilter('auction_id', ['eq'=> $auctionData['entity_id']])
                                            ->addFieldToFilter('flag', ['eq'=>1])
                                            ->addFieldToFilter('shop', ['neq'=>0])->getFirstItem();
                }

                $today = $this->_localeDate->date()->format('m/d/y H:i:s');
                $auctionData['stop_auction_time'] = $this->_localeDate
                                                            ->date(
                                                                new \DateTime($auctionData['stop_auction_time'])
                                                            )->format('Y-m-d H:i:s');
                $auctionData['start_auction_time'] = $this->_localeDate
                                                            ->date(
                                                                new \DateTime($auctionData['start_auction_time'])
                                                            )->format('Y-m-d H:i:s');

                $auctionData['current_time_stamp'] = strtotime($today);
                $auctionData['start_auction_time_stamp'] = strtotime($auctionData['start_auction_time']);
                $auctionData['stop_auction_time_stamp'] = strtotime($auctionData['stop_auction_time']);
                $auctionData['new_auction_start'] = $aucAmtData->getEntityId() ? true : false;
                $auctionData['auction_title'] = __('Bid on ').$curPro->getName();
                // $auctionData['pro_url'] = $curPro->getProductUrl();
                $auctionData['pro_url'] = $this->getUrl().$curPro->getUrlKey().'.html';
                $auctionData['pro_name'] = $curPro->getName();
                $auctionData['pro_buy_it_now'] = $auctionData['buy_it_now'];
                $auctionData['pro_auction'] = $auctionData['auction_status'];
                if ($auctionData['min_amount'] < $auctionData['starting_price']) {
                    $auctionData['min_amount'] = $auctionData['starting_price'];
                }
            } else {
                $auctionData = false;
            }
        }

        $this->_customerSession->setAuctionData($auctionData);
        return $auctionData;
    }

    /**
     * @return array of Auction Configuration Settings
     */
    public function getAuctionConfiguration()
    {
        return $this->_helperData->getAuctionConfiguration();
    }

    /**
     * @return string url of auction form
     */

    public function getAuctionFormAction()
    {
        $curPro = $this->_coreRegistry->registry('current_product');
        if ($curPro) {
            $currentProId = $curPro->getEntityId();
        } else {
            $auctionId = $this->getRequest()->getParam('id');
            $currentProId = $this->getAuctionProId($auctionId);
        }
        return $this->_customerSession->isLoggedIn() ?
                        $this->_urlBuilder->getUrl("lofmpauction/account/login"):
                        $this->_urlBuilder->getUrl('lofmpauction/account/login/', ['id'=>$currentProId]);
    }

    /**
     *
     */
    public function getAuctionDetailAftetEnd($auctionData)
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
                                        ->addFieldToFilter('status', ['eq'=>0]);

        $autoWaittingList = $this->_autoAuctionFactory->create()->getCollection()
                                        ->addFieldToFilter('auction_id', ['eq'=> $auctionId])
                                        ->addFieldToFilter('flag', ['neq'=>1])
                                        ->addFieldToFilter('status', ['eq'=>0]);
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
        return ['watting_user'=> $currentUserWaitingList,'winner' => $currentUserWinner ];
    }

    /**
     * For get Bidding history link
     * @param array $auctionDetail
     * @return string url
     */
    public function getHistoryUrl($auctionData)
    {
        return $this->_urlBuilder->getUrl('lofmpauction/account/history', ['id'=>$auctionData['entity_id']]);
    }

    /**
     * For get Bidding record count
     * @param int $auctionId
     * @return string
     */
    public function getNumberOfBid($auctionId)
    {
        $records = $this->_aucAmountFactory->create()->getCollection()
                                    ->addFieldToFilter('auction_id', ['eq'=>$auctionId]);
        return count($records).__(' Bids');
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
}
