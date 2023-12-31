<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Helper;

// chua edit, cai nay de get configuration setting 


class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
    * @var \Magento\Framework\View\Element\BlockFactory
    */
    protected $_blockFactory;
    /** 
    *@var \Magento\Store\Model\StoreManagerInterface 
    */
    protected $_storeManager;

    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
     /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $_storelocatorid = NULL;

    protected $_storelocatorCollection = NULL;

    protected $_tagid = NULL;

    protected $_categoryid = NULL;

    protected $storeLocatorFactory;

    protected $_sellerFactory;

    protected $_sellers = [];

    CONST GMAP_API_KEY   = 'lofmpstorelocator/general/api_key';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $_sellerFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lofmp\StoreLocator\Model\StoreLocatorFactory $storeLocatorFactory
        ) {
        parent::__construct($context);
        $this->storeLocatorFactory = $storeLocatorFactory;
        $this->_localeDate     = $localeDate;
        $this->_scopeConfig    = $context->getScopeConfig();
        $this->_blockFactory   = $blockFactory;
        $this->_storeManager   = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_objectManager  = $objectManager;
        $this->customerSession = $customerSession;
        $this->_sellerFactory       = $_sellerFactory;
        $this->_resource      = $resource;
    }

     public function generateRewriteUrl($target_path, $request_path, $entity_id = "", $storeId = 0){
        $enable_rewrite_url = $this->getConfig("general/enable_rewrite_url");
        if($enable_rewrite_url) {
            $urlRewriteModel = $this->_objectManager->create('\Magento\UrlRewrite\Model\UrlRewrite');
            //$urlRewriteModel = $this->_urlRewriteFactory->create();
            $collection = $urlRewriteModel->getCollection();
            $entity_id = $entity_id?$entity_id:rand(1, 100000);

            $collection->addFieldToFilter("entity_type", 'lofmp_storelocator');
            $collection->addFieldToFilter("entity_id", (int)$entity_id);
            $collection->addFieldToFilter("store_id", (int)$storeId);

            if(0 < $collection->count()) {
                foreach($collection as $urlItem){
                    $urlRewriteModel2 = $this->_objectManager->create('\Magento\UrlRewrite\Model\UrlRewrite');
                    $urlRewriteModel2->load($urlItem->getId());
                    $urlRewriteModel2->delete();
                }
            }

            /* set current store id */
            $urlRewriteModel->setStoreId((int)$storeId);
            /* set entity id */
            $urlRewriteModel->setEntityId($entity_id);
            /* set entity type */
            $urlRewriteModel->setEntityType('lofmp_storelocator');
            /* set actual url path to target path field */
            $urlRewriteModel->setTargetPath($target_path);
            /* set requested path which you want to create */
            $urlRewriteModel->setRequestPath($request_path);
            /* set current store id */
            $urlRewriteModel->save();
        }
    }

    public function getRewriteUrl($entity_id, $storeId =0){
        $urlRewriteModel = $this->_objectManager->create('\Magento\UrlRewrite\Model\UrlRewrite');
        $collection->addFieldToFilter("entity_type", 'lofmp_storelocator');
        $collection->addFieldToFilter("entity_id", (int)$entity_id);
        $collection->addFieldToFilter("store_id", (int)$storeId);
        if(0 < $collection->count()) {
            $rewriteItem = $collection->getFirstItem();
            return $rewriteItem->getRequestPath();
        } else {
            return '';
        }
    }
    public function getAPIKey(){
        return $this->_scopeConfig->getValue(self::GMAP_API_KEY);
    }

    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'lofmpstorelocator/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
        ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
            );
    }

    public function getFormatDate($date, $type = 'full'){
        $result = '';
        switch ($type) {
            case 'full':
            $result = $this->formatDate($date, \IntlDateFormatter::FULL);
            break;
            case 'long':
            $result = $this->formatDate($date, \IntlDateFormatter::LONG);
            break;
            case 'medium':
            $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
            break;
            case 'short':
            $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
            break;
        }
        return $result;
    }

    public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
        if($length == 0) return $text;
        $text = ($is_striped==true)?strip_tags($text):$text;
        if(strlen($text) <= $length){
            return $text;
        }
        $text = substr($text,0,$length);
        $pos_space = strrpos($text,' ');
        return substr($text,0,$pos_space).$replacer;
    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getMediaUrl(){
        $storeMediaUrl = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
        ->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $storeMediaUrl;
    }
    public function getCustomerId() {
        $customer = $this->customerSession->getCustomer();
        return $customer->getId();
    }
    public function getSellerIDByCustomer() {
        if(!isset($this->_sellerId)){
            $objectManager       = \Magento\Framework\App\ObjectManager::getInstance ();
            $seller = $objectManager->get ( 'Lof\MarketPlace\Model\Seller' )->load ( $this->getCustomerId(), 'customer_id' );
            $this->_sellerId = $seller->getId();
        }
        return $this->_sellerId;
    }

    public function getSellerLocator( $storelocator_id) {
        $storelocator = $this->storeLocatorFactory->create()->load((int)$storelocator_id, 'storelocator_id');
        return  $storelocator?$storelocator->getData():[];
    }

    public function getStoreLocator(){
        if(!$this->_storelocatorCollection){
            $collection = $this->storeLocatorFactory->create()->getCollection();
            $sellerId = (int)$this->getSellerIDByCustomer();
            $collection->addFieldToFilter("seller_id", $sellerId)
                        ->setOrder('storelocator_id', "ASC");
            $this->_storelocatorCollection = $collection;
        }
        return $this->_storelocatorCollection;
    }

    public function getStoreLocatorId(){
        if(!$this->_storelocatorid){
            $connection = $this->_resource->getConnection();
            $select = 'SELECT storelocator_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator') . ' WHERE seller_id = ' .(int)$this->getSellerIDByCustomer() . ' ORDER BY storelocator_id ASC';
            $this->_storelocatorid = $connection->fetchAll($select);
        }
        return $this->_storelocatorid;
    }

    public function getId($name){
        $connection = $this->_resource->getConnection();
        $select = 'SELECT DISTINCT storelocator_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator') . ' WHERE name = "' .$name. '" and is_active = 1';
        $array = $connection->fetchAll($select);
            if($array)
            return $array['0']['storelocator_id']; 
       
    }
    public function getSelerid(){
        $collection = $this->_sellerFactory->create()->getCollection();
        $collection->addFieldToFilter("status", 1);
        $collection->setPageSize(1000); //limit 1000 sellers
        $collection->setCurPage(1);

        $_locationData = $collection->getData();
        
        $_resultData = array();
        foreach ($_locationData as $result) {
            $_resultData[]    =   $result['seller_id'];
        }
        return array_unique($_resultData);
    }

    public function getSellerInfo($sellerId){
        if(!isset($this->_sellers[$sellerId])){
            $this->_sellers[$sellerId] = $this->_sellerFactory->create()->load((int)$sellerId);
        }
        return $this->_sellers[$sellerId];
    }

    public function getTagId($id){
        $connection = $this->_resource->getConnection();
        $select = 'SELECT tag_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator_tag') . ' WHERE storelocator_id = ' .$id. ' ORDER BY tag_id ASC';
        $this->_tagid = $connection->fetchAll($select);
        return $this->_tagid;
    }
    
    public function get_tagid($id) { 
        $connection = $this->_resource->getConnection();
            $select = 'SELECT tag_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator_tag') . ' WHERE storelocator_id = ' .$id. ' ORDER BY tag_id ASC';
            $array = $connection->fetchAll($select);
          $result = array_column($array, 'tag_id');         
        return $result; 
    } 

    public function getCategoryId($id){
        $connection = $this->_resource->getConnection();
        $select = 'SELECT category_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator_category') . ' WHERE storelocator_id = ' .$id. ' ORDER BY category_id ASC';
        $this->_categoryid = $connection->fetchAll($select);
        
        return $this->_categoryid;
    }

    public function get_CategoryId($id){
        $connection = $this->_resource->getConnection();
        $select = 'SELECT category_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator_category') . ' WHERE storelocator_id = ' .$id. ' ORDER BY category_id ASC';
        $array = $connection->fetchAll($select);
        $result = array_column($array, 'category_id');         
        return $result; 
    }

    public function getTag( $tag_id) {
        $objectManager       = \Magento\Framework\App\ObjectManager::getInstance ();
        $Tag = $objectManager->get ( 'Lofmp\StoreLocator\Model\Tag' )->load ( 
            $tag_id, 'tag_id' );
        return  $Tag->getData();
    }


    public function getProductIDbySellerid( $sellerid) {
        $connection = $this->_resource->getConnection();
        $select = 'SELECT product_id FROM ' . $this->_resource->getTableName('lof_marketplace_product') . ' WHERE seller_id = ' .$sellerid. ' ORDER BY product_id ASC';
        $productid = $connection->fetchAll($select);
        $result = array_column($productid, 'product_id');
        return $result;
    }


    public function getDistance($addressFrom, $addressTo, $unit){
        //Change address format
        $formattedAddrFrom = str_replace(' ','+',$addressFrom);
        $formattedAddrTo = str_replace(' ','+',$addressTo);
        
        //Send request and receive json data
        $geocodeFrom = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.$this->getAPIKey().'&address='.urlencode($formattedAddrFrom).'&sensor=false');
        $outputFrom = json_decode($geocodeFrom);
        $geocodeTo = file_get_contents('https://maps.google.com/maps/api/geocode/json?key='.$this->getAPIKey().'&address='.urlencode($formattedAddrTo).'&sensor=false');
        $outputTo = json_decode($geocodeTo);

        //Get latitude and longitude from geo data 
        if(isset($outputFrom->results[0]->geometry)&&isset($outputTo->results[0]->geometry)){
                $latitudeFrom = $outputFrom->results[0]->geometry->location->lat;
                $longitudeFrom = $outputFrom->results[0]->geometry->location->lng;
                $latitudeTo = $outputTo->results[0]->geometry->location->lat;
                $longitudeTo = $outputTo->results[0]->geometry->location->lng;
                
                //Calculate distance from latitude and longitude
                $theta = $longitudeFrom - $longitudeTo;
                $dist = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $unit = strtoupper($unit);
                if ($unit == "K") {
                    return $miles * 1.609344;
                } else if ($unit == "N") {
                    return $miles * 0.8684;
                } else {
                    return $miles;
                }
        }
        else{
            
            return 'F';
        }
    }
    public function getSellerAddress($sellerid){
            $connection = $this->_resource->getConnection();
            $select = 'SELECT address , address2  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator') . ' WHERE seller_id = ' .$sellerid . ' AND is_active = 1 ORDER BY seller_id ASC';
            $address = array_column($connection->fetchAll($select),'address');
            $address2 = array_column($connection->fetchAll($select),'address2');
            $result = array_unique(array_filter(array_merge($address, $address2)));

        return $result;
    }
    public function getSellerCity($sellerid){
            $connection = $this->_resource->getConnection();
            $select = 'SELECT country , city  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator') . ' WHERE seller_id = ' .$sellerid . ' ORDER BY seller_id ASC';
            $result = $connection->fetchAll($select);
           
        return $result;
    }
    public function getSellerIDByStorelocator($id){
            $connection = $this->_resource->getConnection();
            $select = 'SELECT seller_id  FROM ' . $this->_resource->getTableName('lofmp_storelocator_storelocator') . ' WHERE is_active = 1  AND storelocator_id = ' .$id ;
           
            $result = array_column($connection->fetchAll($select),'seller_id');
            
        return $result[0];
    }
}