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
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\Rma\Controller\Marketplace\Rmareport;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManager;

use Lofmp\Rma\Model\Rma;

class Reason extends \Magento\Framework\App\Action\Action
{

    protected $_resultPageFactory;

    protected $_storelocatorCollection;

    protected $_objectManager;

    protected $_helper;

    protected $_session = null;

    protected $_sellerFactory = null;

     protected $_frontendUrl;
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
        Rma     $storelocatorCollection,
        StoreManager       $storeManager,
        \Magento\Customer\Model\Session $sellerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl
    ) {

        $this->_resultPageFactory      = $resultPageFactory;
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_storeManager           = $storeManager;
        $this->_objectManager          = $context->getObjectManager();
        $this->_session = $sellerSession;
        $this->_sellerFactory = $sellerFactory;
         $this->_frontendUrl = $frontendUrl;
        parent::__construct($context);
    }

    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }
    /**
     * Index
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();

        $sellerId = $this->_session->getId();
        $status = $this->_sellerFactory->create()->load($sellerId, 'customer_id')->getStatus();

        if ($this->_session->isLoggedIn() && $status == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif ($this->_session->isLoggedIn() && $status == 0) {
            $this->_redirect($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirect($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
