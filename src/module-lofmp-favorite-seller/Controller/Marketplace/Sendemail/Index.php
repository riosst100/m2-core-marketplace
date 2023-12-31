<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Controller\Marketplace\Sendemail;

use Magento\Framework\App\Action\Context;

class Index extends  \Magento\Framework\App\Action\Action {

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Lofmp\FavoriteSeller\Helper\ConfigData $configData
     */
    protected $configData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lofmp\FavoriteSeller\Helper\ConfigData $configData
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lofmp\FavoriteSeller\Helper\ConfigData $configData
    ) {
        parent::__construct ($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->configData = $configData;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute() {
        if ($this->configData->isEnable()) {
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

        } else {
            $this->messageManager->addNotice(__('You dont have permission to access this feature.'));
            return $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/catalog/dashboard/'));
        }
    }
}
