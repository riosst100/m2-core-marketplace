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

use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\Auction\Model\WinnerDataFactory;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class CheckoutCartUpdateItemsAfter
 * @package Lof\Auction\Observer
 */
class CheckoutCartUpdateItemsAfter implements ObserverInterface
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
     * @var Configurable
     */
    private $_configurableProTypeModel;

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
     * @param Configurable $configurableProTypeModel
     * @param WinnerDataFactory $winnerData
     * @param ProductFactory $auctionProductFactory
     */
    public function __construct(
        Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        RequestInterface $request,
        ManagerInterface $messageManager,
        StockStateInterface $stockStateInterface,
        Configurable $configurableProTypeModel,
        WinnerDataFactory $winnerData,
        ProductFactory $auctionProductFactory
    ) {

        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_stockStateInterface = $stockStateInterface;
        $this->_configurableProTypeModel = $configurableProTypeModel;
        $this->_winnerData = $winnerData;
        $this->_auctionProductFactory = $auctionProductFactory;
    }

    /**
     * Sales quote add item event handler.
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $maxqty = 0;
        $mainArray = [];
        if ($this->_customerSession->isLoggedIn()) {
            $cart = $this->_checkoutSession->getQuote()->getAllItems();
            $info = $observer->getInfo();
            $customerid = $this->_customerSession->getCustomerId();
            $params = $this->_request->getParams();
            foreach ($cart as $item) {
                $proIds[] = $item->getProductId();
            }
            foreach ($cart as $item) {
                $product = $item->getProduct();
                $productId = $item->getProductId();
                if ($product->getTypeId() == 'configurable') {
                    $childPro = $this->_configurableProTypeModel
                        ->getChildrenIds($product->getId());

                    $childProIds = isset($childPro[0]) ? $childPro[0] : [0];

                    $biddingCollection = $this->_auctionProductFactory->create()->getCollection()
                        ->addFieldToFilter('product_id', ['in' => $childProIds])
                        ->addFieldToFilter('auction_status', AuctionStatus::STATUS_TIME_END);
                    if (count($biddingCollection)) {
                        foreach ($biddingCollection as $bidProduct) {
                            $proId = $bidProduct->getProductId();
                            if (in_array($proId, $proIds)) {
                                $productId = $bidProduct->getProductId();
                                break;
                            }
                        }
                    }
                }
                if ($this->isAuctionProduct($productId)) {
                    $bidProCol = $this->_winnerData->create()->getCollection()
                        ->addFieldToFilter('product_id', $productId)
                        ->addFieldToFilter('status', AuctionStatus::STATUS_PROCESS)
                        ->addFieldToFilter('complete', 0)
                        ->setOrder('auction_id');
                    if (count($bidProCol)) {
                        foreach ($bidProCol as $winner) {
                            $maxqty += $winner->getMaxQty();
                            $customerArray['max'] = $winner->getMaxQty();
                            $customerArray['min'] = $winner->getMinQty();
                            $mainArray[$winner->getCustomerId()] = $customerArray;
                        }
                    }
                    if (array_key_exists($customerid, $mainArray)) {
                        $customerMaxQty = $mainArray[$customerid]['max'];
                        $customerMinQty = $mainArray[$customerid]['min'];
                        if ($item->getQty() <= $customerMaxQty && $item->getQty() >= $customerMinQty) {
                            $item->setQty($item->getQty());
                            $this->saveObj($item);
                        } elseif ($item->getQty() > $customerMaxQty) {
                            $item->setQty($customerMaxQty);
                            $this->saveObj($item);
                            $this->_messageManager
                                ->addNotice(
                                    __('Maximum ' . $customerMaxQty . ' quantities are allowed to purchase this item.')
                                );
                        } elseif ($item->getQty() < $customerMinQty) {
                            $item->setQty($customerMinQty);
                            $this->saveObj($item);
                            $this->_messageManager
                                ->addNotice(
                                    __('Minimum ' . $customerMinQty . ' quantities are allowed to purchase this item.')
                                );
                        }
                    } else {
                        $stockQty = $this->_stockStateInterface->getStockQty($productId);
                        $availqty = $stockQty - $maxqty;
                        //var_dump($info[$item->getId()]);
                        if ($availqty > 0) {
                            if ($info[$item->getId()] && array_key_exists('qty', $info[$item->getId()])) {
                                if ($info[$item->getId()]['qty'] >= $availqty) {
                                    $item->setQty($availqty);
                                    $this->_messageManager
                                        ->addNotice(
                                            __('Maximum ' . $availqty . ' quantities are allowed to purchase this item.')
                                        );
                                } else {
                                    $item->setQty($info[$item->getId()]['qty']);
                                }
                                $this->saveObj($item);
                            }
                        } else {
                            $default_qty = 1;
                            if ($info && isset($info[$item->getId()]) && isset($info[$item->getId()]['qty'])) {
                                $default_qty = $info[$item->getId()]['qty'];
                            }
                            $item_qty = isset($params['qty']) ? $params['qty'] : $default_qty;
                            $item->setQty($item->getQty() - $item_qty);
                            $this->saveObj($item);
                            $this->_messageManager->addNotice('you can not add this quantity for purchase.');
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
