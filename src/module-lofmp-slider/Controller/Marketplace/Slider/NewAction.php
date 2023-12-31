<?php

namespace Lofmp\Slider\Controller\Marketplace\Slider;

use Magento\Framework\App\Action\Context;

class NewAction extends \Magento\Framework\App\Action\Action
{
    protected $session;

    protected $resultPageFactory;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    protected $_actionFlag;

    protected $sellerFactory;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct ($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag =  $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
    }

      public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }

    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
