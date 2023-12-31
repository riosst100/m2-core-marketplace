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
namespace Lofmp\StoreLocator\Controller\Index; 

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Lofmp\StoreLocator\Model\ResourceModel\StoreLocator\CollectionFactory;
use Lofmp\StoreLocator\Helper\Image;

class Locations extends \Magento\Framework\App\Action\Action {


    protected $_resultPageFactory;
    protected $_storelocatorCollection;
    protected $_objectManager;
    protected $_helper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CollectionFactory $storelocatorCollection,
        Image $helper,
        \Lofmp\StoreLocator\Helper\Data $storeLocatorHelper
    ) {
        $this->_resultPageFactory      = $resultPageFactory;
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_helper                 = $helper;
        $this->_objectManager          = $context->getObjectManager();
        $this->storeLocatorHelper      = $storeLocatorHelper;
        parent::__construct($context);
    } 

    /**
     * Index
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $storeManager = $this->_objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeId = $storeManager->getStore()->getStoreId();

        $enable_rewrite_url = $this->storeLocatorHelper->getConfig('general/enable_rewrite_url');
        $route = $this->storeLocatorHelper->getConfig('general/route');
        $route = $route?$route."/":"storelocator/";
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Lofmp\StoreLocator\Model\StoreLocator');

        // 2. Initial checking
        if ($id) {
            $_jsonLocatorData = array();
            $model->load($id);
            if (!$model->getId()) {
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $result = $model->getData();
            unset($result['posts']);
             $tagid = implode( ",",$this->storeLocatorHelper->get_tagid($result['storelocator_id']));
            $categoryid = implode( ",", $this->storeLocatorHelper->get_CategoryId($result['storelocator_id']));

            $store_url = $this->_helper->getBaseUrl() . $route . str_replace(" ", "-", $result['name']);
            if($enable_rewrite_url && isset($result['seo_url']) && $result['seo_url']) {
                $store_url = $this->_helper->getBaseUrl().$result['seo_url'];
            }
            $_jsonLocatorData    =   array(
                'id'        =>  $result['storelocator_id'],
                'name'      =>  $result['name'],
                'lng'       =>  $result['lng'],
                'lat'       =>  $result['lat'],
                'address'   =>  $result['address'],
                'address2'  =>  $result['address2'],
                'link'      =>  '', //$result['link'],
                'image'     =>  $this->_helper->resizeImage($result['image'], 128, 128),
                'telephone' =>  isset($result['telephone'])?$result['telephone']:'',
                'email'     =>  $result['email'],
                'website'   =>  '', //$result['website'],
                'city'      =>  $result['city'],
                'country'   =>  $result['country'],
                'zipcode'   =>  $result['zipcode'],
                'hours'     =>  $result['hours'],
                'hours1'    =>  $result['hours1'],
                'hours2'    =>  $result['hours2'],
                'hours3'    =>  $result['hours3'],
                'hours4'    =>  $result['hours4'],
                'hours5'    =>  $result['hours5'],
                'hours6'    =>  $result['hours6'],
                'linkedin'  =>  $result['linkedin'],
                'facebook'  =>  $result['facebook'],
                'twitter'   =>  $result['twitter'],
                'youtube'   =>  $result['youtube'],
                'vimeo'     =>  $result['vimeo'],
                'tag'       =>  $tagid,
                'category'  =>  $categoryid,
                'href'      =>  $store_url,
                'color'     =>  $result['color'],
                'fontClass' =>  $result['fontClass']
                );
            echo json_encode(array($_jsonLocatorData));
            exit();
        }

        // 3. list data
        $_data = $this->_storelocatorCollection->create();
        $_locationData = $_data->getData();
        $_jsonLocationData = array();
        foreach ($_locationData as $result) {
            if($result['is_active']!=0){
                     $tagid = implode( ",",$this->storeLocatorHelper->get_tagid($result['storelocator_id']));
                    $categoryid = implode( ",", $this->storeLocatorHelper->get_CategoryId($result['storelocator_id']));

                 $store_url = $this->_helper->getBaseUrl() . $route . str_replace(" ", "-", $result['name']);
                if($enable_rewrite_url && isset($result['seo_url']) && $result['seo_url']) {
                    $store_url = $this->_helper->getBaseUrl().$result['seo_url'];
                }

                 $_jsonLocationData[]    =   array(
                    'id'        =>  $result['storelocator_id'],
                    'name'      =>  $result['name'],
                    'lng'       =>  $result['lng'],
                    'lat'       =>  $result['lat'],
                    'address'   =>  $result['address'],
                    'address2'  =>  $result['address2'],
                    'link'      =>  $result['link'],
                    'image'     =>  $this->_helper->resizeImage($result['image'], 128, 128),
                    'telephone' =>  isset($result['telephone'])?$result['telephone']:'',
                    'email'     =>  $result['email'],
                    'website'   =>  isset($result['website'])?$result['website']:'',
                    'city'      =>  $result['city'],
                    'country'   =>  $result['country'],
                    'zipcode'   =>  $result['zipcode'],
                    'hours'     =>  $result['hours'],         
                    'hours1'    =>  empty($result['hours1'])?'':$result['hours1'],
                    'hours2'    =>  empty($result['hours2'])?'':$result['hours2'],
                    'hours3'    =>  empty($result['hours3'])?'':$result['hours3'],
                    'hours4'    =>  empty($result['hours4'])?'':$result['hours4'],
                    'hours5'    =>  empty($result['hours5'])?'':$result['hours5'],
                    'hours6'    =>  empty($result['hours6'])?'':$result['hours6'],
                    'linkedin'  =>  empty($result['linkedin'])?'':$result['linkedin'],
                    'facebook'  =>  empty($result['facebook'])?'':$result['facebook'],
                    'twitter'   =>  empty($result['twitter'])?'':$result['twitter'],
                    'youtube'   =>  empty($result['youtube'])?'':$result['youtube'],
                    'vimeo'     =>  empty($result['vimeo'])?'':$result['vimeo'],
                    'tag'      =>  $tagid,
                    'category'  =>  $categoryid,
                    'href'      =>  $store_url,
                    'color'     =>  trim($result['color']),
                    'fontClass' =>  trim($result['fontClass'])
                    );
        }
     }
     echo json_encode($_jsonLocationData);
     exit();
 }

}