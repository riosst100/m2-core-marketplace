<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://Landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.Landofcoder.com/)
 * @license    http://www.Landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\StoreLocator;

use Magento\Framework\View\Element\Template\Context;
use Lofmp\StoreLocator\Helper\Data;
use Lofmp\StoreLocator\Model\ResourceModel\StoreLocator\CollectionFactory;

use Lofmp\StoreLocator\Model\ResourceModel\StoreLocator;

class Details extends \Magento\Framework\View\Element\Template
{

    protected $_coreRegistry = null;
    protected $_storelocatorData;
    protected $_scopeConfig;
    protected $_storelocatorCollection;
    protected $_tagCollection;
    protected $_modelStoreLocator;

    public function __construct(
                \Magento\Framework\Registry $registry,
                Context       $context,
                Data          $storelocatorData,
                CollectionFactory  $storelocatorCollection,
                \Lofmp\StoreLocator\Model\ResourceModel\Tag\CollectionFactory $tagCollection,

                \Lofmp\StoreLocator\Model\StoreLocatorFactory $modelStoreLocator,
                array         $data = []  
            )
    {
        $this->_coreRegistry           = $registry;
        $this->_storelocatorData       = $storelocatorData;
        $this->_scopeConfig            = $context->getScopeConfig();
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_tagCollection          = $tagCollection;

        $this->_modelStoreLocator = $modelStoreLocator;

        parent::__construct($context, $data);

    }

    public function _tohtml(){

        $mdata = $this->getStoreLocator();
        
        $this->assign('data', $mdata );
        return parent::_toHtml();
    }

    protected function getStoreLocator(){
        $mdata = array();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $storelocator = $this->_modelStoreLocator->create()->load($id);
            $mdata = $storelocator->getData();
        }
        return $mdata;
    }

    protected function _prepareLayout()
    {   
        $page_title =__('Store Locator');
        $mdata = $this->getStoreLocator();
        if (empty($mdata['pagetitle'])) {
            $page_title = $mdata['name'];
        } else {
            $page_title = $mdata['pagetitle'];
        }

        $meta_description = $mdata['meta_description'];
        $meta_keywords = $mdata['keywords'];

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
        $this->pageConfig->addBodyClass('storelocator');
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
        $mdata = $this->getStoreLocator();

        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');

        $store_page_title = $this->getConfig('general/page_title');
        $store_page_title = $store_page_title?$store_page_title:__('Store Locator');
        $route = $this->getConfig('general/route');
        $route = $route?$route:"storelocator";

        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $page_title = $mdata['name'];
        $show_breadcrumbs = true;

        if($show_breadcrumbs && $breadcrumbsBlock){
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
             );
            $breadcrumbsBlock->addCrumb(
                'list',
                [
                    'label' => $store_page_title,
                    'title' => __('Return to Store Locator'),
                    'link' => $this->getUrl($route)
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'view',
                [
                    'label' => $page_title,
                    'title' => $page_title,
                    'link' => ''
                ]
             );
        }
    }

    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->_scopeConfig->getValue(
            'lofmpstorelocator/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }


    public function getTagCollection(){

        $_data = $this->_tagCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
             $_resultData[]    =   array(
                'value'        =>  $result['name'],
                'label'        =>  $result['name'],
            );
        }
        return $_resultData;
    }

    public function getMediaUrl(){
        $test = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        return $test;
    }
}