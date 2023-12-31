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

namespace Lof\Auction\Helper;

use Lof\Auction\Model\Amount;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuction;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\WinnerDataFactory;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime;

/**
 * Class ProcessWinner
 *
 * @package Lof\Auction\Helper
 */
class ProcessWinner extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Customer
     */
    protected $_customer;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var Email
     */
    protected $_helperEmail;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var Amount
     */
    protected $_auctionAmount;

    /**
     * @var AutoAuction
     */
    protected $_autoAuction;

    /**
     * @var WinnerDataFactory
     */
    protected $_winnerData;

    /**
     * @param Customer $customer
     * @param Product $product
     * @param Data $helperData
     * @param Email $helperEmail
     * @param ProductFactory $auctionProductFactory
     * @param AmountFactory $auctionAmount
     * @param AutoAuctionFactory $autoAuction
     * @param WinnerDataFactory $winnerData
     * @param Context $context
     */
    public function __construct(
        Customer $customer,
        Product $product,
        Data $helperData,
        Email $helperEmail,
        ProductFactory $auctionProductFactory,
        AmountFactory $auctionAmount,
        AutoAuctionFactory $autoAuction,
        WinnerDataFactory $winnerData,
        Context $context
    ) {
        $this->_customer = $customer;
        $this->_product = $product;
        $this->_helperData = $helperData;
        $this->_helperEmail = $helperEmail;
        $this->_auctionProductFactory = $auctionProductFactory;
        $this->_auctionAmount = $auctionAmount;
        $this->_autoAuction = $autoAuction;
        $this->_winnerData = $winnerData;
        parent::__construct($context);
    }

    /**
     * @param null $auction_id
     * @return array
     */
    public function runAuction($auction_id = null)
    {
        $return = [];
        $return['error'] = [];
        $return['success'] = [];
        $auctionActProCollection = $this->_auctionProductFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'auction_status',
                [
                    "in" => [
                        AuctionStatus::STATUS_WINNER_NOT_DEFINED,
                        AuctionStatus::STATUS_PROCESS,
                    ],
                ]
            );
        if ($auction_id) {
            if (!is_array($auction_id)) {
                $auction_id = explode(",", $auction_id);
            }
            $auctionActProCollection->addFieldToFilter("entity_id", ["in" => $auction_id]);
        }
        if ($auctionActProCollection->count()) {
            foreach ($auctionActProCollection as $auctionActPro) {
                $result = $this->processFindWinner($auctionActPro->getProductId(), $auctionActPro);
                if ($result) {
                    $return['success'][] = __("Process auction ID %1 is completely.", $auctionActPro->getEntityId());
                } else {
                    $return['error'][] = __("Can not Process auction ID %1.", $auctionActPro->getEntityId());
                }
            }
        } else {
            $return['error'][] = __("Available Auction products is empty at now!");
        }

        return $return;
    }

    /**
     * @param        $auctionBid
     * @param string $auctionType
     * @return bool
     */
    public function setWinnerManual($auctionBid, $auctionType = "manual")
    {
        //only set winner for not complete and canceled auction bidding
        if ($auctionBid && in_array($auctionBid->getStatus(), [AuctionStatus::STATUS_TIME_END, AuctionStatus::STATUS_PROCESS, AuctionStatus::STATUS_WINNER_NOT_DEFINED])) {
            $auctionConfig = $this->_helperData->getAuctionConfiguration();
            $auction_id = $auctionBid->getAuctionId();
            $productId = $auctionBid->getProductId();
            $today = $this->_helperData->getTimezoneDateTime();//date('Y-m-d H:i:s');
            $auctionActPro = null;
            $winDataTemp = [];
            if ($auction_id) {
                $auctionActPro = $this->_auctionProductFactory->create()->load($auction_id);
                if ($auctionActPro->getEntityId()) {
                    $auction_status = $auctionActPro->getAuctionStatus();
                    //if this auction is complete or canceled, will return and not process
                    if (in_array ($auction_status, [AuctionStatus::STATUS_COMPLETE, AuctionStatus::STATUS_TIME_END])) {
                        $auctionActPro = null;
                    }
                }
            }
            if ($auctionActPro) {
                $resPrice = $auctionActPro->getReservePrice();
                $starPrice = $auctionActPro->getStartingPrice();
                $resPrice = $resPrice ? $resPrice : $starPrice;
                $winDataTemp['auction_id'] = $auctionActPro->getEntityId();
                $winDataTemp['customer_id'] = $auctionBid->getCustomerId();

                if ($auctionType == "manual") {
                    $winDataTemp['amount'] = $auctionBid->getAuctionAmount();
                    $winDataTemp['type'] = 'normal';
                } elseif ($auctionType == "auto") {
                    $winDataTemp['amount'] = $auctionBid->getAmount();
                    $winDataTemp['type'] = 'auto';
                }

                if ($winDataTemp['amount'] && ($resPrice <= $winDataTemp['amount'])) {
                    $auctionBid->setWinningPrice($winDataTemp['amount']);
                    if ($winDataTemp['type'] == 'auto') {
                        $auctionBid->setFlag(1);
                        if ($auctionConfig['enable_winner_notify_email']) { //Send email notification to winner
                            $this->_helperEmail->sendWinnerMail(
                                $winDataTemp['customer_id'],
                                $productId,
                                $winDataTemp['amount']
                            );
                        }
                        $auctionBid->setStatus(AuctionStatus::STATUS_TIME_END);
                        $auctionBid->setId($auctionBid->getEntityId());
                        $this->saveObj($auctionBid);
                    } elseif ($winDataTemp['type'] == 'normal') {
                        $auctionBid->setWinningStatus(AuctionStatus::STATUS_PROCESS);
                        /** send notification mail to winner */
                        if ($auctionConfig['enable_winner_notify_email']) {
                            $this->_helperEmail->sendWinnerMail(
                                $winDataTemp['customer_id'],
                                $productId,
                                $winDataTemp['amount']
                            );
                        }
                        $auctionBid->setStatus(AuctionStatus::STATUS_TIME_END);
                        $auctionBid->setId($auctionBid->getEntityId());
                        $this->saveObj($auctionBid);
                    }

                    //save winner record in winner data table
                    $winnerDataModel = $this->_winnerData->create();
                    $auctionModelData = $auctionActPro->getData();
                    $auctionModelData['customer_id'] = $winDataTemp['customer_id'];
                    $auctionModelData['auction_id'] = $auctionModelData['entity_id'];
                    $auctionModelData['win_amount'] = $winDataTemp['amount'];
                    unset($auctionModelData['entity_id']);

                    $winnerDataModel->setData($auctionModelData);
                    $winnerDataModel->setData('status', AuctionStatus::STATUS_PROCESS);
                    $this->saveObj($winnerDataModel);

                    $auctionActPro->setData("stop_auction_time", $today);
                    $auctionActPro->setAuctionStatus(AuctionStatus::STATUS_TIME_END);
                    $auctionActPro->setData("customer_id", $winDataTemp['customer_id']);
                } else {
                    $auctionActPro->setAuctionStatus(AuctionStatus::STATUS_WINNER_NOT_DEFINED);
                }
                $auctionActPro->setId($auctionActPro->getEntityId());
                $this->saveObj($auctionActPro);

                return true;
            }
        }
        return false;
    }

    /**
     * @param $stopDateString
     * @return bool
     */
    public function checkIsStopDateEnd($stopDateString)
    {
        $datestop = strtotime($stopDateString);
        $today = $this->_helperData->getTimezoneDateTime();
        $current = strtotime($today);
        $difference = $datestop - $current;
        if ($difference <= 0) {
            return true;
        }

        return false;
    }

    /**
     * @param      $productId
     * @param null $auctionActPro
     * @return bool
     */
    public function processFindWinner($productId, $auctionActPro = null)
    {
        $winDataTemp = [];
        $auctionConfig = $this->_helperData->getAuctionConfiguration();
        if (!$auctionActPro) {
            $auctionActPro = $this->_auctionProductFactory->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $productId])
                ->addFieldToFilter('auction_status',
                [
                    "in" => [
                        AuctionStatus::STATUS_WINNER_NOT_DEFINED,
                        AuctionStatus::STATUS_PROCESS
                    ]
                ])
                ->setOrder('entity_id')
                ->getFirstItem();
        }
        if ($auctionActPro->getEntityId()) {
            //if this auction is complete or canceled, will return and not process
            $bidId = $auctionActPro->getEntityId();
            $resPrice = $auctionActPro->getReservePrice();
            $auction_stop_time = $this->_helperData->getTimezoneDateTime($auctionActPro->getStopAuctionTime());
            $auction_start_time = $this->_helperData->getTimezoneDateTime($auctionActPro->getStartAuctionTime());
            $datestop = strtotime($auction_stop_time);
            $datestart = strtotime($auction_start_time);
            if ($datestop >= $datestart) {
                $winDataTemp['auction_id'] = $bidId;
                $today = $this->_helperData->getTimezoneDateTime();
                $current = strtotime($today);
                $difference = $datestop - $current;

                if ($difference <= 0) {
                    $bidArray = [];
                    $totalOfBid = 0;
                    $bidAmountList = $this->_auctionAmount->create()->getCollection()
                        ->addFieldToFilter('product_id', ['eq' => $productId])
                        ->addFieldToFilter('auction_id', ['eq' => $bidId])
                        ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS])
                        ->setOrder('auction_amount', 'ASC');
                    foreach ($bidAmountList as $bidAmt) {
                        $bidArray[$bidAmt->getCustomerId()] = $bidAmt->getAuctionAmount();
                    }
                    $totalOfBid = count($bidArray);
                    //arsort($bidArray);
                    $autoBidArray = [];
                    /***/
                    if (count($bidArray)) {
                        $customerIds = array_keys($bidArray, max($bidArray));
                        $winDataTemp['customer_id'] = $customerIds[0];
                        $winDataTemp['amount'] = max($bidArray);
                        $winDataTemp['type'] = 'normal';
                    }
                    if ($auctionConfig['auto_enable'] && $auctionActPro->getAutoAuctionOpt()) {
                        $autoBidList = $this->_autoAuction->create()->getCollection()
                            ->addFieldToFilter('auction_id', ['eq' => $bidId])
                            ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS]);
                        if (count($bidArray)) {
                            $autoBidList->addFieldToFilter('amount', ['gteq' => max($bidArray)]);
                            $winDataTemp['amount'] = max($bidArray);
                        } else {
                            $starPrice = $auctionActPro->getStartingPrice();
                            $winDataTemp['amount'] = $resPrice ? $resPrice : $starPrice;
                        }

                        $totalOfBid += count($autoBidList);

                        if (count($autoBidList)) {
                            foreach ($autoBidList as $autoBid) {
                                $custId = $autoBid->getCustomerId();
                                $autoBidArray[$custId] = $autoBid->getAmount();
                            }
                            $customerIds = array_keys($autoBidArray, max($autoBidArray));
                            $winDataTemp['customer_id'] = $customerIds[0];
                            $winDataTemp['type'] = 'auto';
                        }

                        if ($totalOfBid == 0) {
                            // TODO: get absentee bid and place an auto bid, then make him as winner
                        }

                        if ($auctionConfig['increment_auc_enable'] && $auctionActPro->getIncrementOpt()
                            && isset($winDataTemp['customer_id']) && $winDataTemp['type'] = 'auto') {
                            $incVal = $this->_helperData->getIncrementPriceAsRange($winDataTemp['amount']);
                            $incMinPrice = $incVal ? $incVal : 0;
                            $winDataTemp['amount'] = $winDataTemp['amount'] + $incMinPrice;
                        }

                    }

                    if (isset($winDataTemp['customer_id']) && $winDataTemp['amount'] && ($resPrice <= $winDataTemp['amount'])) {
                        if ($winDataTemp['type'] == 'auto') {
                            $autoBiddList = $this->_autoAuction->create()->getCollection()
                                ->addFieldToFilter('auction_id', ['eq' => $bidId])
                                ->addFieldToFilter('status', AuctionStatus::STATUS_PROCESS);

                            foreach ($autoBiddList as $autoBid) {
                                if ($autoBid->getCustomerId() == $winDataTemp['customer_id']) {
                                    $autoBidAmount = $autoBid->getAmount();
                                    if ($autoBidAmount > $winDataTemp['amount']) {
                                        $winDataTemp['amount'] = $autoBidAmount;
                                    }
                                    $autoBid->setFlag(AuctionStatus::STATUS_PROCESS);
                                    $autoBid->setWinningPrice($winDataTemp['amount']);
                                    /** send notification mail to winner */
                                    if ($auctionConfig['enable_winner_notify_email']) {
                                        $this->_helperEmail->sendWinnerMail(
                                            $winDataTemp['customer_id'],
                                            $productId,
                                            $winDataTemp['amount']
                                        );
                                    }
                                }
                                $autoBid->setStatus(AuctionStatus::STATUS_TIME_END);
                                $autoBid->setId($autoBid->getEntityId());
                                $this->saveObj($autoBid);
                            }
                        } elseif ($winDataTemp['type'] == 'normal') {
                            $normalBidList = $this->_auctionAmount->create()->getCollection()
                                ->addFieldToFilter('product_id', ['eq' => $productId])
                                ->addFieldToFilter('auction_id', ['eq' => $bidId])
                                ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS]);

                            foreach ($normalBidList as $normalBid) {
                                if ($normalBid->getCustomerId() == $winDataTemp['customer_id']) {
                                    $normalBidAmount = $normalBid->getAuctionAmount();
                                    if ($normalBidAmount > $winDataTemp['amount']) {
                                        $winDataTemp['amount'] = $normalBidAmount;
                                    }
                                    $normalBid->setWinningStatus(AuctionStatus::STATUS_PROCESS);
                                    /** send notification mail to winner */
                                    if ($auctionConfig['enable_winner_notify_email']) {
                                        $this->_helperEmail->sendWinnerMail(
                                            $winDataTemp['customer_id'],
                                            $productId,
                                            $winDataTemp['amount']
                                        );
                                    }
                                }
                                $normalBid->setStatus(AuctionStatus::STATUS_TIME_END);
                                $normalBid->setId($normalBid->getEntityId());
                                $this->saveObj($normalBid);
                            }
                        }

                        //save winner record in winner data table
                        $winnerDataModel = $this->_winnerData->create();
                        $auctionModel = $this->_auctionProductFactory->create()->load($bidId)->getData();
                        $auctionModel['customer_id'] = $winDataTemp['customer_id'];
                        $auctionModel['auction_id'] = $auctionModel['entity_id'];
                        $auctionModel['win_amount'] = $winDataTemp['amount'];//$auctionModel['min_amount'];
                        $auctionModel['status'] = AuctionStatus::STATUS_PROCESS;
                        unset($auctionModel['entity_id']);
                        $winnerDataModel->setData($auctionModel);

                        $this->saveObj($winnerDataModel);
                        $auctionActPro->setAuctionStatus(AuctionStatus::STATUS_TIME_END);
                        $auctionActPro->setData("customer_id", $winDataTemp['customer_id']);
                    } else {
                        $auctionActPro->setAuctionStatus(AuctionStatus::STATUS_WINNER_NOT_DEFINED);
                    }

                    $auctionActPro->setId($auctionActPro->getEntityId());

                    $this->saveObj($auctionActPro);
                } else {
                    $winnerDataModel = $this->_winnerData->create()->getCollection()
                        ->addFieldToFilter('product_id', ['eq' => $productId])
                        ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS])
                        ->getFirstItem();

                    if ($winnerDataModel->getEntityId()) {
                        $bidDay = !$winnerDataModel->getDays() ? 1 : (int)$winnerDataModel->getDays();
                        $current = strtotime($this->_helperData->getTimezoneDateTime());
                        $stopAuctionTime = $this->_helperData->getTimezoneDateTime($winnerDataModel->getStopAuctionTime());
                        $day = strtotime($stopAuctionTime . ' + ' . $bidDay . ' days');
                        $difference = $day - $current;
                        if ($difference < 0) {
                            $winnerDataUpdateModel = $this->_winnerData->create()->load($winnerDataModel->getEntityId());
                            $winnerDataUpdateModel->setStatus(AuctionStatus::STATUS_TIME_END);
                            $this->saveObj($winnerDataUpdateModel);
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }

    /**
     * saveObj
     *
     * @param Object
     * @return void
     */
    private function saveObj($object)
    {
        $object->save();
    }
}
