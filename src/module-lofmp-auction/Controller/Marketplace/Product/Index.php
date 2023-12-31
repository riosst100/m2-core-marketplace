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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Controller\Marketplace\Product;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Url;

class Index extends Action
{

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var Url
     */
    protected $_frontendUrl;

    /**
     * @var ActionFlag
     */
    protected $_actionFlag;
    /**
     * @var SellerFactory
     */
    private $sellerFactory;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Lofmp\Auction\Helper\Data
     */
    protected $helperData;

    /**
     * Font constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory
     * @param PageFactory $resultPageFactory
     * @param Url $frontendUrl
     * @param \Lofmp\Auction\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SellerFactory $sellerFactory,
        PageFactory $resultPageFactory,
        Url $frontendUrl,
        \Lofmp\Auction\Helper\Data $helperData
    ) {
        parent::__construct($context);
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;
        $this->helperData = $helperData;
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
    /**
     * Redirect to URL
     * @param string $url
     * @return ResponseInterface
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
     * @return Page
     */
    public function execute()
    {
        if ($this->helperData->isEnabledForSeller()) {
            $customerSession = $this->session;
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();

            if ($customerSession->isLoggedIn() && $status == 1) {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->prepend((__('Auctions')));
                return $resultPage;
            } elseif ($customerSession->isLoggedIn() && $status == 0) {
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
            } else {
                $this->messageManager->addNotice(__('You must have a seller account to access'));
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
            }
        } else {
            $this->messageManager->addNotice(__('You dont have permission to access this feature.'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/catalog/dashboard/'));
        }
    }
}
