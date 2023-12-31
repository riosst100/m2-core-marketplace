<?php


namespace   Lofmp\StoreLocator\Controller\Search;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class User
 * @package Ves\FaceSupportLive\Controller\Index
 */

class Index extends \Magento\Framework\App\Action\Action
{

   protected $_coreRegistry = null;
   protected $_resultPageFactory;

    /**
     * User constructor.
     * @param Context $context
     */
    public function __construct(
         \Magento\Framework\Registry $_coreRegistry,
         PageFactory        $resultPageFactory,
        Context $context
    ) {
        $this->_coreRegistry = $_coreRegistry;
        $this->_resultPageFactory      = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @param $user_email
     * @return bool
     */
   
    public function execute()
    {
      $resultPage = $this->_resultPageFactory->create();
       // $resultPage->addHandle('storelocator_index_index'); 
        return $resultPage;
        
    }
}
