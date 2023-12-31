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

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\WinnerDataFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class SalesQuoteAddItem
 * @package Lof\Auction\Observer
 */
class SalesQuoteAddItem implements ObserverInterface
{

    /**
     * @var ProductFactory
     */

    protected $_messageManager;

    /**
     * @var ProductFactory
     */

    protected $_customerSession;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var Data
     */

    protected $_dataHelper;

    /**
     * @var WinnerDataFactory
     */

    protected $_winnerData;

    /**
     * @param ManagerInterface $messageManager
     * @param Session $customerSession
     * @param RequestInterface $request
     * @param ProductFactory $auctionProductFactory
     * @param Data $dataHelper
     * @param WinnerDataFactory $winnerData
     */
    public function __construct(
        ManagerInterface $messageManager,
        Session $customerSession,
        RequestInterface $request,
        ProductFactory $auctionProductFactory,
        Data $dataHelper,
        WinnerDataFactory $winnerData
    ) {

        $this->_messageManager = $messageManager;
        $this->_customerSession = $customerSession;
        $this->_request = $request;
        $this->_auctionProductFactory = $auctionProductFactory;
        $this->_dataHelper = $dataHelper;
        $this->_winnerData = $winnerData;
    }

    /**
     * Sales quote add item event handler.
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {

        if ($this->_customerSession->isLoggedIn()) {
            //$price = '';
            $customerId = $this->_customerSession->getCustomerId();
            $item = $observer->getQuoteItem();
            $product = $item->getProduct();
            $productId = $product->getId();
            if ($product->getTypeId() == 'configurable') {
                $superAttribute = $this->_request->getParam('super_attribute');
                if ($superAttribute) {
                    if (count($superAttribute) > 0) {
                        //get product id according to attribute values
                        $pro = $product->getTypeInstance(true)->getProductByAttributes($superAttribute, $product);
                        $productId = $pro->getId();
                    }
                }
            }
            $biddingProductCollection = $this->_winnerData->create()->getCollection()
                ->addFieldToFilter('product_id', ['eq' => $productId])
                ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS])
                ->addFieldToFilter('customer_id', ['eq' => $customerId])
                ->addFieldToFilter('complete', ['eq' => 0])
                ->setOrder('auction_id')->getFirstItem();

            if ($biddingProductCollection->getEntityId()) {
                $auctionId = $biddingProductCollection->getAuctionId();
                $auctionPro = $this->_auctionProductFactory->create()->load($auctionId);
                if ($auctionPro->getEntityId() && $auctionPro->getAuctionStatus() == AuctionStatus::STATUS_TIME_END) {
                    $bidWinPrice = $biddingProductCollection->getWinAmount();

                    $item->setOriginalCustomPrice($bidWinPrice);
                    $item->setCustomPrice($bidWinPrice);
                    $item->getProduct()->setIsSuperMode(true);
                }
            }
        }
        return $this;
    }
}
