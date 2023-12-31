<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Controller\Marketplace\Shipping;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\Url;
use Lofmp\TableRateShipping\Helper\Data;
use Magento\Framework\Url as FrontendUrl;

class View extends Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Url
     */
    protected $_customerUrl;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var FrontendUrl
     */
    protected $_frontendUrl;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Url $customerUrl
     * @param Data $helper
     * @param FrontendUrl $frontendUrl
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        Url $customerUrl,
        Data $helper,
        FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_customerUrl = $customerUrl;
        $this->helper = $helper;
        $this->_frontendUrl = $frontendUrl;
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_customerUrl->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->_customerSession->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Shipping rate view page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $seller = $this->helper->getSellerByCustomer();
        $partnerId = $seller && isset($seller['seller_id']) ? $seller['seller_id'] : 0;
        if (!$partnerId) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
            return;
        }
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Marketplace Table Rate Shipping'));
        return $resultPage;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }
}
