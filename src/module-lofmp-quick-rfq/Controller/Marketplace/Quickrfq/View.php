<?php

namespace Lofmp\Quickrfq\Controller\Marketplace\Quickrfq;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

class View extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    protected $session;

    protected $resultPageFactory;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    protected $_actionFlag;

    protected $sellerFactory;

    protected $helper;

    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Registry $registry
    ) {
        parent::__construct($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag =  $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper           = $sellerHelper;
        $this->_coreRegistry = $registry;
    }

    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $id = $this->getRequest()->getParam('quickrfq_id');
            $model = $this->_objectManager->create('Lof\Quickrfq\Model\Quickrfq');

            // 2. Initial checking
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addError(__('This quote no longer exists.'));
                    $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
                } else {
                    $data = $model->getData();
                    if ($data["seller_id"] != $this->helper->getSellerId()) {
                        $this->messageManager->addError(__('This Quote no longer exists.'));
                        $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
                    }
                    // 4. Register model to use later in blocks
                    $this->_coreRegistry->register('quickrfq', $model);
                }
            } else {
                $this->messageManager->addError(__('This Quote no longer exists.'));
                $this->_redirectUrl($this->getFrontendUrl('marketplace/quickrfq/quickrfq'));
            }
           /** @var \Magento\Framework\View\Result\PageFactory $resultPage */
           $resultPage = $this->resultPageFactory->create();
           $resultPage->getConfig()->getTitle()->prepend(__('Manage RFQs'));
           return $resultPage;

        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
