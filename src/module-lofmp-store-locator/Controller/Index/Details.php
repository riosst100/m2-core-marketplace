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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

use Magento\Framework\Controller\Result\ForwardFactory;

use Magento\Store\Model\StoreManager;

use Lofmp\StoreLocator\Model\StoreLocator;
use Lofmp\StoreLocator\Helper\Data;

class Details extends \Magento\Framework\App\Action\Action {

    protected $_resultPageFactory;
    protected $_resultForwardFactory;
    protected $_storelocatorCollection;
    protected $_objectManager;
    protected $_helper;

     /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
     protected $_coreRegistry = null;

    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context       $context               
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory     
     * @param \Lofmp\StoreLocator\Model\StoreLocator        $storelocatorCollection 
     * @param \Magento\Store\Model\StoreManager           $storeManager          
     * @param \Lofmp\StoreLocator\Helper\Data               $helper                
     */
    public function __construct(
        Context            $context,
        PageFactory        $resultPageFactory,
        ForwardFactory     $resultForwardFactory,
        StoreLocator       $storelocatorCollection,
        StoreManager       $storeManager,
        \Magento\Framework\Registry $registry,
        Data               $helper
        ) {
        $this->_resultPageFactory      = $resultPageFactory;
        $this->_resultForwardFactory   = $resultForwardFactory;
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_storeManager           = $storeManager;
        $this->_objectManager          = $context->getObjectManager();
        $this->_helper                 = $helper;
        $this->_coreRegistry           = $registry;
        parent::__construct($context);
    } 
    /**
     * Index
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        //echo "storelocator/index/details"; die;
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Lofmp\StoreLocator\Model\StoreLocator');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
            $this->_coreRegistry->register("current_storelocator", $model);
        }

        $resultPage = $this->_resultPageFactory->create();
        if(!$this->_helper->getConfig('general/enable')){
            $resultPage = $this->_resultForwardFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Store Locator Details'));
            return $resultPage;
        }
        return $resultPage;
    }
}