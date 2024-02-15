<?php

namespace CoreMarketplace\StoreLocator\Block\StoreLocator;

use Magento\Framework\View\Element\Template\Context;
use Lofmp\StoreLocator\Helper\Data;
use Lofmp\StoreLocator\Model\ResourceModel\StoreLocator\CollectionFactory;

class Index extends \Lofmp\StoreLocator\Block\StoreLocator\Index
{
    protected $_storelocatorData;
    protected $_scopeConfig;
    protected $_storelocatorCollection;
    protected $_tagCollection;
    protected $_countryFactory;
    protected $_categoryCollection;

    public function __construct(
        Context $context,
        Data $storelocatorData,
        CollectionFactory $storelocatorCollection,
        \Lofmp\StoreLocator\Model\ResourceModel\Tag\CollectionFactory $tagCollection,
        \Lofmp\StoreLocator\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Magento\Framework\App\Request\Http $request,
        \Lof\MarketPlace\Block\Product\ListProduct  $blockproduct,
        array $data = []  
    ) {
        $this->_storelocatorData       = $storelocatorData;
        $this->_scopeConfig            = $context->getScopeConfig();
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_tagCollection          = $tagCollection;
        $this->_categoryCollection     = $categoryCollection;
        $this->_countryFactory         = $countryFactory;
        $this->request = $request;
        parent::__construct(
            $context,
            $storelocatorData,
            $storelocatorCollection,
            $tagCollection,
            $categoryCollection,
            $countryFactory,
            $request,
            $blockproduct,
            $data
        );
     
    }

    public function getOperatingStatusList() 
    {
        $statusList = [
            [
                'value' => 'closed',
                'label' => __('Closed')
            ],
            [
                'value' => 'open',
                'label' => __('Open')
            ]
        ];

        return $statusList;
    }

    public function getDayNames() 
    {
        $dayNames = [
            [
                'code' => 'mon',
                'label' => 'Monday'
            ],
            [
                'code' => 'tue',
                'label' => 'Tuesday'
            ],
            [
                'code' => 'wed',
                'label' => 'Wednesday'
            ],
            [
                'code' => 'thu',
                'label' => 'Thursday'
            ],
            [
                'code' => 'fri',
                'label' => 'Friday'
            ],
            [
                'code' => 'sat',
                'label' => 'Saturday'
            ],
            [
                'code' => 'sun',
                'label' => 'Sunday'
            ]
        ];

        return $dayNames;
    }
   
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->_scopeConfig->getValue(
            'lofmpstorelocator/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function getCountry(){

        $_data = $this->_storelocatorCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
            $_resultData[]    =   $result['country'];
        }
        return array_unique($_resultData);
    }

    public function getListCity(){

        $_data = $this->_storelocatorCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
            $_resultData[]    =   $result['city'];
        }
        return array_unique($_resultData);
    }

    public function getListCountry(){

         $optionsc = $this->_countryFactory->toOptionArray();


        return $optionsc;
    }
    
    public function getListZipCode(){

        $_data = $this->_storelocatorCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
            $_resultData[]    =   $result['zipcode'];
        }
        return array_unique($_resultData);
    }

    public function getCategoryCollection(){

        $_data = $this->_categoryCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {

            $_resultData[]    =   array(
                'value'        =>  $result['category_id'],
                'label'        =>  $result['name'],
                );
        }
        return $_resultData;
    }

    public function getTagCollection() {

        $_data = $this->_tagCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
          if($result['status']==1){
            $_resultData[]    =   array(
                'value'        =>  $result['tag_id'],
                'label'        =>  $result['name'],
                );
            }   
        }
        return $_resultData;
    }

    public function getLocationDataJson(){
        $_data = $this->_storelocatorCollection->create();
        $_locationData = $_data->getData();
        $_jsonLocationData = array();
        foreach ($_locationData as $result) {
            $_jsonLocationData[]    =   array(
                'id'          =>  $result['storelocator_id'],
                'name'        =>  $result['name'],
                'description' =>  $result['description'],
                'lng'         =>  $result['lng'],
                'lat'         =>  $result['lat'],
                'address'     =>  $result['address'],
                'address2'    =>  $result['address2'],
                'link'        =>  $result['link'],
                'image'       =>  $result['image'],
                'telephone'   =>  $result['telephone'],
                'email'       =>  $result['email'],
                'website'     =>  $result['website'],
                'city'        =>  $result['city'],
                'zipcode'     =>  $result['zipcode'],
                'state'       =>  $result['state'],
                'hours'       =>  $result['hours'],
                'linkedin'    =>  $result['linkedin'],
                'facebook'    =>  $result['facebook'],
                'twitter'     =>  $result['twitter'],
                'youtube'     =>  $result['youtube'],
                'vimeo'       =>  $result['vimeo'],

                );
        }

        return json_encode($_jsonLocationData);
    }

    public function getMediaUrl(){
        $test = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        return $test;
    }

    public function _prepareLayout(){
        $is_category_view = $this->getData("is_category_view");
        if(!$is_category_view){
            $this->pageConfig->addBodyClass('storelocator');
        }

        $page_title = $this->getConfig("general/page_title");
        $page_title = $page_title?$page_title:__('Seller Store Locator');
        $meta_description = $this->getConfig("general/seo_keywords");
        $meta_keywords = $this->getConfig("general/seo_description");

        $this->_addBreadcrumbs();

        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $brand
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = $this->getConfig('general/page_title');
        $show_breadcrumbs = true;

        if($show_breadcrumbs && $breadcrumbsBlock){
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $baseUrl
                ]
                );

            $breadcrumbsBlock->addCrumb(
                'list',
                [
                'label' => $page_title,
                'title' => $page_title,
                'link'  => ''
                ]
                );
        }
    }
}