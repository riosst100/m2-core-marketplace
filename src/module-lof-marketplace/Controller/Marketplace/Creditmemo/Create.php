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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Marketplace\Creditmemo;

use Magento\Framework\App\Action\Context;

class Create extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var \Lof\MarketPlace\Model\OrderFactory
     */
    protected $_sellerOrder;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param \Lof\MarketPlace\Model\OrderFactory $sellerOrder
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\OrderFactory $order,
        \Lof\MarketPlace\Model\OrderFactory $sellerOrder,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_order = $order;
        $this->_sellerOrder = $sellerOrder;
        $this->_coreRegistry = $coreRegistry;
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
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $sellerId = $seller->getId();
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $order = $this->getOrder();
            if (!$order->getId()) {
                $this->messageManager->addErrorMessage(__('This order no longer exists.'));
                return $this->_redirect('catalog/sales/order');
            }

            $sellerOrder = $this->getSellerOrder();
            if ($sellerId != $sellerOrder->getSellerId()) {
                $this->messageManager->addErrorMessage(__('Permission denied.'));
                return $this->_redirect('catalog/sales/order');
            }

            if ($order->getStatus() !== 'complete' && $order->getStatus() !== 'processing') {
                $this->messageManager->addErrorMessage(__('The order status is not Completed.'));
                return $this->_redirect('catalog/sales/order');
            }

            if (!$order->canCreditmemo()) {
                $this->messageManager->addErrorMessage(__('The order can not create creditmemo.'));
                return $this->_redirect('catalog/sales/order');
            }

            $this->_coreRegistry->register('mp_current_order', $order);
            $this->_coreRegistry->register('mp_current_seller_order', $sellerOrder);
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
        return null;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order->create()->load($this->getOrderId());
    }

    /**
     * @return \Lof\MarketPlace\Model\Order
     */
    public function getSellerOrder()
    {
        return $this->_sellerOrder->create()->load($this->getOrderId(), 'order_id');
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return (int)$this->getRequest()->getParam('id');
    }
}
