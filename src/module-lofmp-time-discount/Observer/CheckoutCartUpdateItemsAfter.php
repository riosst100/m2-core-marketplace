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
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class CheckoutCartUpdateItemsAfter implements ObserverInterface
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


    protected $helper;
    /**
     * @param \Magento\Customer\Model\Session         $customerSession
     * @param \Magento\Checkout\Model\Session         $checkoutSession
     * @param RequestInterface                        $request
     * @param ManagerInterface                        $messageManager
     * @param StockStateInterface                     $stockStateInterface
     * @param \Lofmp\TimeDiscount\Model\ProductFactory        $lofmptimediscountProductFactory
     * @param \Lofmp\TimeDiscount\Model\WinnerDataFactory $winnerData
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        RequestInterface $request,
        ManagerInterface $messageManager,
        StockStateInterface $stockStateInterface,
        \Lofmp\TimeDiscount\Model\Product $timediscount,
        \Lofmp\TimeDiscount\Model\Quote $time_quote,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\TimeDiscount\Helper\Data $helperData
    ) {
        $this->helper = $helper;
        $this->time_quote = $time_quote;
        $this->timediscount = $timediscount;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_stockStateInterface = $stockStateInterface;
        $this->_helperData = $helperData;
    }


    /**
     * Sales quote add item event handler.
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $cart = $this->_checkoutSession->getQuote()->getAllItems();
        foreach ($cart as $item) {
            $product = $item->getProduct();
            $productId = $item->getProductId();
            $item_id = $item->getId();
            $time_quote = $this->time_quote->load($item_id,'item_id'); 
            $qty = $item->getQty();
            if($time_quote) {
                $time_quote->setQty($qty);
                $time_quote->save();
            }
          
        }
        return $this;
    }

    
}
