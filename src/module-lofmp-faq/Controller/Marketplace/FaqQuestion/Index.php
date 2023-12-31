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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Controller\Marketplace\FaqQuestion;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $_session;

    protected $_coreRegistry = null;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    protected $_actionFlag;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url $frontendUrl
    ) {
        $this->_actionFlag      = $context->getActionFlag();
        $this->_coreRegistry    = $coreRegistry;
        $this->_session         = $customerSession;
        $this->_frontendUrl     = $frontendUrl;
        parent::__construct($context);
    }

    public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }

    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->_session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Create new Faq Question
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->_session;
        if(!$customerSession->isLoggedIn()) {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
