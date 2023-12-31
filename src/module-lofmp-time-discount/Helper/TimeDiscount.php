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

namespace Lofmp\TimeDiscount\Helper;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Checkout\Model\CartFactory;
use Lofmp\TimeDiscount\Model\QuoteFactory;

class TimeDiscount extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var CartFactory
     */
    protected $cartFactory;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    protected $_sellers = [];

    /**
     * Construct
     * 
     * @param \Magento\Framework\App\Helper\Context $context
     * @param SellerFactory $sellerFactory
     * @param Data $helperData
     * @param CartFactory $cartFactory
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        SellerFactory $sellerFactory,
        Data $helperData,
        CartFactory $cartFactory,
        QuoteFactory $quoteFactory
    ) {
        $this->helperData = $helperData;
        $this->sellerFactory = $sellerFactory;
        $this->quoteFactory = $quoteFactory;
        $this->cartFactory = $cartFactory;
        parent::__construct($context);
    }

    public function sellerById($seller_id){
        if(!isset($this->_sellers[$seller_id])){
            $this->_sellers[$seller_id] = $this->sellerFactory->create()->load($seller_id, 'seller_id' );
        }
        return $this->_sellers[$seller_id];
    }

    public function getQuote(){
        $cart = $this->cartFactory->create();
        return $this->quoteFactory->create()->load($cart->getQuote()->getId(),'quote_id');
    }

}
