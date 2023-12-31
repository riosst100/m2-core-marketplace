<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\PriceComparison\Controller\Marketplace\Product ;

use Magento\Framework\App\Action\Context;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\SalesFactory
     */
    protected $sellerFactory;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
     /**
      * @var \Lof\PriceComparison\Helper\Data
      */
    protected $_assignHelper;
    /**
     *
     * @param Context $context
     * @param Magento\Framework\App\Action\Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Lofmp\PriceComparison\Helper\Data $helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_assignHelper = $helper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
    }
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->_assignHelper->isEnabled() && $this->_assignHelper->getConfig("general_settings/allow_seller_manage")) {
            $customerSession = $this->session;
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();

            if ($customerSession->isLoggedIn() && $status == 1) {
                $checkProduct = $this->_assignHelper->checkProduct(1);

                if ($checkProduct['error']) {
                    $this->messageManager->addError($checkProduct['msg']);
                    return $this->resultRedirectFactory->create()->setPath('*/*/view');
                } else {
                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__('Assign Product'));
                    return $resultPage;
                }
            } elseif ($customerSession->isLoggedIn() && $status == 0) {
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
            } else {
                $this->messageManager->addNotice(__('You must have a seller account to access'));
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
            }
        } else {
            $this->messageManager->addNotice(__('You dont have permision to access this feature.'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/catalog/dashboard'));
    }
    }
}
