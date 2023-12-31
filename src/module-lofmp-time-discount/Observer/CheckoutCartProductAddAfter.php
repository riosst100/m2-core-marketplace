<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\CatalogInventory\Api\StockStateInterface;

class CheckoutCartProductAddAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */

    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */

    protected $_checkoutSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */

    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */

    protected $_messageManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */

    protected $_stockStateInterface;


    protected $time_quote;

    protected $timediscountFactory;

    protected $helper;
    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param   \Magento\Checkout\Model\Session $checkoutSession
     * @param   RequestInterface $request
     * @param   ManagerInterface $messageManager
     * @param   StockStateInterface $stockStateInterface
     * @param   \Lofmp\TimeDiscount\Model\ProductFactory $timediscount
     * @param   \Lofmp\TimeDiscount\Model\Quote $time_quote
     * @param   \Lof\MarketPlace\Helper\Seller $helper
     * @param   \Lofmp\TimeDiscount\Helper\Data $helperData
     * @param   \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        RequestInterface $request,
        ManagerInterface $messageManager,
        StockStateInterface $stockStateInterface,
        \Lofmp\TimeDiscount\Model\ProductFactory $timediscount,
        \Lofmp\TimeDiscount\Model\Quote $time_quote,
        \Lof\MarketPlace\Helper\Seller $helper,
        \Lofmp\TimeDiscount\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->helper = $helper;
        $this->time_quote = $time_quote;
        $this->timediscountFactory = $timediscount;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_stockStateInterface = $stockStateInterface;
        $this->_helperData = $helperData;
        $this->date = $date;

    }

    /**
     * Sales quote add item event handler.
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $today = $this->_helperData->getTimezoneDateTime();
        $time_start = $this->_request->getPost('time_start');
        $time_end = $this->_request->getPost('time_end');
        $item = $observer->getQuoteItem();
        $customerId = $this->_customerSession->getCustomerId();
        $product = $item->getProduct();
        $productId = $product->getId();
        $qty = $item->getQty();

        $quote_id = $item->getQuote()->getId();
        $item_id = $item->getId();

        $timediscount = $this->timediscountFactory->create()->load($productId, 'product_id')->setOrder('order', 'ASC');
        $seller_id = $this->helper->getSellerIdByProduct($productId);
        $i=0;

        if (count($timediscount->getData()) > 0 && $timediscount->getData('data')) {
            foreach ($timediscount->getData('data') as $key => $timeslot) {
                $timeStart = $this->_helperData->getTimezoneDateTime($timeslot['start']);
				$timeEnd = $this->_helperData->getTimezoneDateTime($timeslot['end']);
                if (strtotime($today) >= strtotime($timeStart) && strtotime($today) <= strtotime($timeEnd)) {
                    $i++;
                    if ($timeslot['type'] == 'percent') {
                        $price = $product->getPrice()*(1-$timeslot['discount']/100);
                    } else {
                        $price = $product->getPrice() - $timeslot['discount'];
                    }
                    $time_quote = $this->time_quote;
                    if ($this->_helperData->issetQuote($productId, $quote_id)) {
                        $time_quote->load($this->_helperData->issetQuote($productId, $quote_id));
                    }
                    $time_quote
                        ->setItemId($item_id)
                        ->setCustomerId($customerId)
                        ->setSellerId($seller_id)
                        ->setQty($qty)
                        ->setProductId($productId)
                        ->setPrice($price)
                        ->setQuoteId($quote_id)
                        ->setTimeStart($time_start)
                        ->setTimeEnd($time_end);
                    $time_quote->save();
                    $item->setOriginalCustomPrice($price);
                    $item->setCustomPrice($price);
                    $item->getProduct()->setIsSuperMode(true);
                    break;
                } else {
                    $item->setOriginalCustomPrice($product->getPrice());
                    $item->setCustomPrice($product->getPrice());
                    $item->getProduct()->setIsSuperMode(true);

                    $time_quote = $this->time_quote;
                    if ($this->_helperData->issetQuote($productId, $quote_id)) {
                        $time_quote->load($this->_helperData->issetQuote($productId, $quote_id));
                    }
                    $time_quote
                        ->setItemId($item_id)
                        ->setCustomerId($customerId)
                        ->setSellerId($seller_id)
                        ->setQty($qty)
                        ->setProductId($productId)
                        ->setPrice($product->getPrice())
                        ->setQuoteId($quote_id)
                        ->setTimeStart($time_start)
                        ->setTimeEnd($time_end);
                    $time_quote->save();
                }
            }
        }

        return $this;
    }
}
