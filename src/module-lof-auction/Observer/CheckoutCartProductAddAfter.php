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
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Setup\Exception;

/**
 * Class CheckoutCartProductAddAfter
 * @package Lof\Auction\Observer
 */
class CheckoutCartProductAddAfter implements ObserverInterface
{
    /**
     * @var Session
     */

    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */

    protected $_checkoutSession;

    /**
     * @var RequestInterface
     */

    protected $_request;

    /**
     * @var ManagerInterface
     */

    protected $_messageManager;

    /**
     * @var StockStateInterface
     */

    protected $_stockStateInterface;

    /**
     * @var WinnerDataFactory
     */

    protected $_winnerData;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;
    /**
     * @var Data
     */
    private $_helperData;

    /**
     * @var bool[]|int[]|array|mixed
     */
    protected $_auctionProduct = [];

    /**
     * @param Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param StockStateInterface $stockStateInterface
     * @param WinnerDataFactory $winnerData
     * @param ProductFactory $auctionProductFactory
     * @param Data $helperData
     */
    public function __construct(
        Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        RequestInterface $request,
        ManagerInterface $messageManager,
        StockStateInterface $stockStateInterface,
        WinnerDataFactory $winnerData,
        ProductFactory $auctionProductFactory,
        Data $helperData
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_stockStateInterface = $stockStateInterface;
        $this->_winnerData = $winnerData;
        $this->_helperData = $helperData;
        $this->_auctionProductFactory = $auctionProductFactory;
    }

    /**
     * Sales quote add item event handler.
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
        $productId = $product->getId();
        $auction = $this->_helperData->getAuctionByProduct($productId);
        $todayUtc = $this->_helperData->getTimezoneDateTime();
        if ($todayUtc < $auction->getStartAuctionTime()) {
            return;
        }
        if (!$this->canAddToCart($productId)) {
            $this->_messageManager->addErrorMessage(
                __('You cannot add this product to the cart because it is being auctioned')
            );
            throw new CouldNotSaveException(
                __('You cannot add this product to the cart because it is being auctioned')
            );
        } else {
            if ($this->_customerSession->isLoggedIn()) {
                $customerId = $this->_customerSession->getCustomerId();
                $productIdChild = 0;
                if ($product->getTypeId() == 'configurable') {
                    $superAttribute = $this->_request->getParam('super_attribute');
                    if ($superAttribute) {
                        if (count($superAttribute) > 0) {
                            //get product id according to attribute values
                            $pro = $product->getTypeInstance(true)->getProductByAttributes($superAttribute, $product);
                            $productIdChild = $pro->getId();
                        }
                    }
                }
                $biddingProductCollection = $this->_winnerData->create()->getCollection()
                    ->addFieldToFilter('product_id', ['in' => [$productIdChild, $productId]])
                    ->addFieldToFilter('status', ['eq' => AuctionStatus::STATUS_PROCESS])
                    ->addFieldToFilter('customer_id', ['eq' => $customerId])
                    ->addFieldToFilter('complete', ['eq' => 0])
                    ->setOrder('auction_id')
                    ->getFirstItem();

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
                $maxQty = 0;
                $mainArray = [];
                $params = $this->_request->getParams();
                $cartData = $this->_checkoutSession->getQuote()->getAllItems();
                foreach ($cartData as $cart) {
                    if (isset($params['product']) && ($cart->getProduct()->getId() == $params['product'])) {

                        //Process with configurable product
//                        if ($product->getTypeId() == 'configurable') {
//                            if (isset($params['super_attribute'])) {
//                                if (count($params['super_attribute']) > 0) {
//                                    $pro = $product->getTypeInstance(true)
//                                        ->getProductByAttributes($params['super_attribute'], $product);
//                                    $productId = $pro->getId();
//                                }
//                            }
//                        }
                        if ($this->isAuctionProduct($productId)) {
                            $auctionId = $this->_helperData->getActiveAuctionId($productId);
                            $bidProCol = $this->_winnerData->create()->getCollection()
                                ->addFieldToFilter('product_id', $productId)
                                ->addFieldToFilter('complete', 0)
                                ->addFieldToFilter('status', AuctionStatus::STATUS_PROCESS)
                                ->addFieldToFilter('customer_id', $customerId)
                                ->addFieldToFilter('auction_id', $auctionId);

                            if (count($bidProCol)) {
                                foreach ($bidProCol as $winner) {
                                    $maxQty += $winner->getMaxQty();
                                    $customerArray['max'] = $winner->getMaxQty();
                                    $customerArray['min'] = $winner->getMinQty();
                                    $mainArray[$winner->getCustomerId()] = $customerArray;
                                }
                            }
                            if (array_key_exists($customerId, $mainArray)) {
                                $customerMaxQty = $mainArray[$customerId]['max'];
                                $customerMinQty = $mainArray[$customerId]['min'];
                                $itemQty = $cart->getQty();

                                if ($itemQty > $customerMaxQty) {
                                    $msg = 'Only ' . $customerMaxQty . ' quantities are allowed to purchase this item.';
                                    $this->_messageManager->addNotice(__($msg));
                                    $cart->setQty($customerMaxQty);
                                    $this->saveObj($cart);
                                } elseif ($itemQty < $customerMinQty) {
                                    $this->_messageManager
                                        ->addNotice(
                                            __('At least '.$customerMinQty.' quantities are allowed to purchase this item.')
                                        );
                                    $cart->setQty($customerMinQty);
                                    $this->saveObj($cart);
                                }
                            } else {
                                $stockQty = $this->_stockStateInterface->getStockQty($productId);
                                $availqty = $stockQty - $maxQty;
                                if ($availqty > 0) {
                                    if ($cart->getQty() > $availqty) {
                                        $this->_messageManager
                                            ->addNotice(
                                                __('You can add Only ' . $availqty . ' quantities to purchase this item.')
                                            );
                                        $cart->setQty($availqty);
                                        $this->saveObj($cart);
                                    }
                                } else {
                                    $this->_messageManager->addNotice(__('You can not add this quantities for purchase.'));
                                    $qty = isset($params['qty']) ? (int)$params['qty']: 1;
                                    $cart->setQty($cart->getQty() - $qty);
                                    $this->saveObj($cart);
                                }
                            }
                        }
                    }
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

    /**
     * @param $productId
     * @return bool
     */
    public function canAddToCart($productId)
    {
        $auction = $this->_helperData->getAuctionByProduct($productId);
        if ($auction->getData() && $auction->getId()) {
            $this->_auctionProduct[$productId] = true;
            if (!$auction->getProBuyItNow()) {
                return false;
            }
        }
        return true;
    }

    /**
     * check product is auction
     *
     * @param int $productId
     * @return bool|int
     */
    protected function isAuctionProduct($productId)
    {
        if (!isset($this->_auctionProduct[$productId])) {
            $collection = $this->_auctionProductFactory->create()->getCollection()
                            ->addFieldToFilter('product_id', (int)$productId);
            if ($collection->count()) {
                $this->_auctionProduct[$productId] = true;
            }
            $this->_auctionProduct[$productId] = false;

        }
        return $this->_auctionProduct[$productId];
    }
}
