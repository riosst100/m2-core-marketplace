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
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Lofmp\TableRateShipping\Model\ShippingFactory;
use Magento\Customer\Model\Url;
use Magento\Framework\App\RequestInterface;
use Lofmp\TableRateShipping\Helper\Data;
use Magento\Framework\Url as FrontendUrl;

class Delete extends Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ShippingFactory
     */
    protected $_mpshippingModel;

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
     * @param Session $customerSession
     * @param ShippingFactory $mpshippingFactory
     * @param Url $customerUrl
     * @param Data $helper
     * @param FrontendUrl $frontendUrl
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ShippingFactory $mpshippingFactory,
        Url $customerUrl,
        Data $helper,
        FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_mpshippingModel = $mpshippingFactory;
        $this->_customerUrl = $customerUrl;
        $this->helper = $helper;
        $this->_frontendUrl = $frontendUrl;
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $urlModel = $this->_customerUrl;
        $loginUrl = $urlModel->getLoginUrl();
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $seller = $this->helper->getSeller();
        $status = $seller ? $seller->getStatus() : 0;
        if ($this->_customerSession->isLoggedIn() && $status == 1) {
            try {
                $partnerId = $seller ? $seller->getId() : 0;
                if (!$partnerId) {
                    $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
                    return;
                }
                $fields = $this->getRequest()->getParams();
                if (!empty($fields) && $partnerId) {
                    $shippingModel = $this->_mpshippingModel->create()->load($fields['lofmpshipping_id']);
                    if (!empty($shippingModel)
                        && $shippingModel->getId()
                        && $partnerId == $shippingModel->getPartnerId()
                    ) {
                        $shippingModel->delete();
                        $this->messageManager->addSuccessMessage(__('Shipping detail is successfully Deleted!'));
                        return $resultRedirect->setPath('lofmptablerateshipping/shipping/view');
                    } else {
                        $this->messageManager->addErrorMessage(__('No record Found!'));
                        return $resultRedirect->setPath('lofmptablerateshipping/shipping/view');
                    }
                } else {
                    $this->messageManager->addSuccessMessage(__('Please try again!'));
                    return $resultRedirect->setPath('lofmptablerateshipping/shipping/view');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('lofmptablerateshipping/shipping/view');
            }
        } elseif ($this->_customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
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
