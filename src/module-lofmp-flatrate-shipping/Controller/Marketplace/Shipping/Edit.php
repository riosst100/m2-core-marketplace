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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Controller\Marketplace\Shipping;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Lofmp\FlatRateShipping\Model\ShippingmethodFactory;
use Lofmp\FlatRateShipping\Model\ShippingFactory;
use Magento\Customer\Model\Url;
use Lofmp\FlatRateShipping\Helper\Data;
use Lofmp\FlatRateShipping\Controller\Marketplace\Shipping;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Shipping
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var ShippingmethodFactory
     */
    protected $_mpshippingMethod;

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
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param ShippingFactory $mpshippingModel
     * @param Url $customerUrl
     * @param Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ShippingmethodFactory $shippingmethodFactory,
        ShippingFactory $mpshippingModel,
        Url $customerUrl,
        Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_session = $customerSession;
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_mpshippingModel = $mpshippingModel;
        $this->_customerUrl = $customerUrl;
        $this->helper = $helper;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $model = $this->_customerUrl;
        $url = $model->getLoginUrl();
        if (!$this->_session->authenticate($url)) {
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
        $this->_session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('lofmpshipping_id');
        if ($id) {
            $seller = $this->helper->getSeller();
            $status = $seller ? $seller->getStatus() : 0;
            if ($this->_session->isLoggedIn() && $status == 1) {
                try {
                    $model = $this->_mpshippingModel->create()->load($id);

                    if (!$this->validate($id, $model, $seller)) {
                        return $this->_redirect('*/*');
                    }

                    $this->_coreRegistry->register('lofmpflatrateshipping_shipping', $model);

                    $title = $this->_view->getPage()->getConfig()->getTitle();
                    $title->prepend(__('Flat Rate Shipping'));
                    $title->prepend(__('Shipping Rate'));

                    $resultPage = $this->resultPageFactory->create();
                    $resultPage->getConfig()->getTitle()->set(__('Flat Rate Shipping'));
                    return $resultPage;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $this->resultRedirectFactory->create()->setPath('lofmpflatrateshipping/shipping/view');
                }
            } elseif ($this->_session->isLoggedIn() && $status == 0) {
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
            } else {
                $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
            }
        } else {
            return $this->resultRedirectFactory->create()->setPath('lofmpflatrateshipping/shipping/view');
        }
    }

    /**
     * @param $id
     * @param $model
     * @param $seller
     * @return bool
     */
    public function validate($id, $model, $seller)
    {
        if (strpos($this->getRequest()->getRequestUri(), 'lofmpflatrateshipping/shipping/edit') !== false && !$id) {
            $this->messageManager->addErrorMessage(__("Invalid id. Should be numeric value greater than 0"));
            return false;
        }
        if ($id && (!$model->getId())
        ) {
            $this->messageManager->addErrorMessage(__("This table rate shipping does not exist."));
            return false;
        }

        if ($id && $model->getPartnerId() != $seller->getData('seller_id')
        ) {
            $this->messageManager->addErrorMessage(__("You don\'t have permission to edit this shipping"));
            return false;
        }
        return true;
    }
}
