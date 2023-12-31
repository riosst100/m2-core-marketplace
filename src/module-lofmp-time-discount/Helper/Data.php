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
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;
    /**
     * @var customer
     */
    protected $customer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
     /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;
      /**
     * @var  \Lofmp\TimeDiscount\Model\Quote
     */
    protected $time_quote;
     /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    protected $_sellers = [];

    /**
     * Construct
     * @param  \Magento\Framework\App\Helper\Context $context
     * @param  \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param  TimezoneInterface $timezoneInterface
     * @param  PriceHelper $priceHelper
     * @param  \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param  \Magento\Catalog\Model\ProductFactory $productCollectionFactory
     * @param  \Magento\Customer\Model\CustomerFactory $customer
     * @param  \Lofmp\TimeDiscount\Model\Quote $time_quote
     * @param  \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter
     * @param  CustomerSession $customerSession
     * @param  SellerFactory $sellerFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        TimezoneInterface $timezoneInterface,
        PriceHelper $priceHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Lofmp\TimeDiscount\Model\Quote $time_quote,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceFormatter,
        CustomerSession $customerSession,
        SellerFactory $sellerFactory
    ) {
        $this->time_quote = $time_quote;
        $this->_storeManager = $storeManager;
        $this->_dateTime = $dateTime;
        $this->_timezoneInterface = $timezoneInterface;
        $this->priceHelper = $priceHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->customer = $customer;
        $this->priceFormatter = $priceFormatter;
        $this->sellerFactory = $sellerFactory;
        parent::__construct($context);
    }

    public function getDateTime(){
        return $this->_dateTime;
    }
    
    public function getTimezoneName(){
        return $this->_timezoneInterface->getConfigTimezone(\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
    public function getTimezoneDateTime($dateTime = "today"){
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->_dateTime->gmtDate();
        }
        
        $today = $this->_timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }
    public function getProductById($product_id) {
        $collection = $this->productCollectionFactory->create()->load($product_id);
      return $collection;
    }
    public function issetQuote($product_id,$quote_id) {
        $time_quote = $this->time_quote->getCollection()->addFieldToFilter('product_id',$product_id)->addFieldToFilter('quote_id',$quote_id)->getFirstItem();
        
        return $time_quote->getId();
    }
    /**
     * Get current currency code
     *
     * @return string
     */ 
    public function getCurrentCurrencyCode()
    {
      return $this->priceFormatter->getCurrency()->getCurrencyCode();
    }
    
     public function getPriceFomat($price) {
        $currencyCode = $this->getCurrentCurrencyCode();
        return $this->priceFormatter->format(
                    $price,
                    false,
                    null,
                    null,
                    $currencyCode
                );
    }
      /**
      * Get customer id
      * @return customer id
      */
    public function getCustomerById($customer_id) {
        $collection = $this->customer->create()->load($customer_id);
        return $collection;
    }
     /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
     public function getConfig($key, $store = null)
    {
       
        $store = $this->_storeManager->getStore($store);
        $result =  $this->scopeConfig->getValue(
            'lofmptimediscount/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function sellerById($seller_id){
        if(!isset($this->_sellers[$seller_id])){
            $this->_sellers[$seller_id] = $this->sellerFactory->create()->load($seller_id, 'seller_id' );
        }
        return $this->_sellers[$seller_id];
    }

}
