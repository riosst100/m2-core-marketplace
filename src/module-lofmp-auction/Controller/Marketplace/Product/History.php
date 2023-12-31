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

use Lof\Auction\Model\Product;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Url;

class History extends Action
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
    protected $resultFactory;
    /**
     * @var Registry
     */
    private $_coreRegistry;
    /**
     * @var Product
     */
    private $auction;

    /**
     * Font constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory
     * @param PageFactory $resultFactory
     * @param Url $frontendUrl
     * @param Registry $coreRegistry
     * @param Product $auction
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SellerFactory $sellerFactory,
        PageFactory $resultFactory,
        Url $frontendUrl,
        Registry $coreRegistry,
        Product $auction
    ) {
        parent::__construct($context);
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->resultFactory = $resultFactory;
        $this->session = $customerSession;
        $this->_coreRegistry = $coreRegistry;
        $this->auction = $auction;
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
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId, 'customer_id')->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $id = $this->getRequest()->getparam('id');
            $auction = $this->auction->load($id);
            $this->_coreRegistry->register('lofauction_product', $auction);
            $resultLayout = $this->resultFactory->create();
            $resultLayout->getLayout()->getBlock('lofmpauction_product_edit_history');
            return $resultLayout;
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
