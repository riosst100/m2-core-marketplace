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
use Magento\Store\Model\StoreManager;

use Lofmp\StoreLocator\Model\StoreLocator;
use Lofmp\StoreLocator\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action {

    protected $_resultPageFactory;
    protected $_storelocatorCollection;
    protected $_objectManager;
    protected $_helper;
 
    
    /**
     * [__construct description]
     * @param \Magento\Framework\App\Action\Context       $context               
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory     
     * @param \Lof\StoreLocator\Model\StoreLocator        $storelocatorCollection 
     * @param \Magento\Store\Model\StoreManager           $storeManager          
     * @param \Lof\StoreLocator\Helper\Data               $helper                
     */
    public function __construct(
                Context            $context,
                PageFactory        $resultPageFactory,
                StoreLocator       $storelocatorCollection,
                StoreManager       $storeManager,
                Data               $helper
            )
    {

        $this->_resultPageFactory      = $resultPageFactory;
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_storeManager           = $storeManager;
        $this->_objectManager          = $context->getObjectManager();

        parent::__construct($context);
    } 
    /**
     * Index
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        //echo "storelocator/index"; die;
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addHandle('storelocator_index_index'); 
        return $resultPage;
    }
}