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

namespace Lof\Auction\Observer;

;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\Product;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\WinnerDataFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderPlaceAfter
 * @package Lof\Auction\Observer
 */
class SalesOrderPlaceAfter implements ObserverInterface
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var WinnerDataFactory
     */
    protected $_winnerData;

    /**
     * @var Data
     */
    protected $_helperData;
    /**
     * @var Product
     */
    private $_auctionProduct;

    /**
     * @param CustomerSession $customerSession
     * @param WinnerDataFactory $winnerData
     * @param Product $auctionProduct
     * @param Data $helperData
     */
    public function __construct(
        CustomerSession $customerSession,
        WinnerDataFactory $winnerData,
        Product $auctionProduct,
        Data $helperData
    ) {

        $this->_customerSession = $customerSession;
        $this->_winnerData = $winnerData;
        $this->_auctionProduct = $auctionProduct;
        $this->_helperData = $helperData;
    }

    /**
     * after place order event handler.
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $customerId = $this->_customerSession->getCustomerId();
        $order = $observer->getOrder();
        foreach ($order->getAllItems() as $item) {
            $activeAucId = $this->_helperData->getActiveAuctionId($item->getProductId());
            $auctionWinData = $this->_winnerData->create()->getCollection()
                ->addFieldToFilter('product_id', $item->getProductId())
                ->addFieldToFilter('status', AuctionStatus::STATUS_PROCESS)
                ->addFieldToFilter('complete', 0)
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('auction_id', $activeAucId)
                ->getFirstItem();

            if ($auctionWinData->getEntityId()) {
                $winnerBidDetail = $this->_helperData->getWinnerBidDetail($auctionWinData->getAuctionId());
                if ($winnerBidDetail) {
                    //bider bid row update
                    $winnerBidDetail->setShop(1);
                    $this->saveObj($winnerBidDetail);
                    //update winner Data
                    $auctionWinData->setComplete(1);
                    $this->saveObj($auctionWinData);

                    $aucPro = $this->_auctionProduct->load($auctionWinData->getAuctionId());
                    //here we set auction process completely done
                    $aucPro->setAuctionStatus(AuctionStatus::STATUS_COMPLETE);
                    $aucPro->setStatus(AuctionStatus::STATUS_PROCESS);
                    $this->saveObj($aucPro);
                }
            }
        }
        return $this;
    }

    /**
     * saveObj
     * @param Object
     * @return void
     */
    private function saveObj($object)
    {
        $object->save();
    }
}
