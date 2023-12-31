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

namespace Lof\MarketPlace\Controller\Marketplace\Vacation;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

class Index extends \Magento\Customer\Controller\AbstractAccount
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var string
     */
    protected $_formSessionKey = "marketplace_vacation";

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

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
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Lof\MarketPlace\Model\VacationFactory
     */
    protected $vacationFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var mixed|array
     */
    protected $_vacationData = [];

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\VacationFactory $vacationFactory
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\VacationFactory $vacationFactory,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->vacationFactory = $vacationFactory;
        $this->dataPersistor = $dataPersistor;
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
        $status = $seller ? $seller->getStatus() : 0;

        if ($customerSession->isLoggedIn() && $status == 1) {
            $model = $this->getVacation($seller->getId());
            if ($model->getId()) {
                $id = $model->getId();
            } else {
                $id = 0;
            }

            $this->getRequest()->setParams(['vacation_id' => $id]);
            $this->_getRegistry()->register('current_model', $model);
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
     * @return \Lof\MarketPlace\Model\Vacation
     */
    public function getVacation($sellerId)
    {
        if (!isset($this->_vacationData[$sellerId])) {
            $this->_vacationData[$sellerId] = $this->vacationFactory->create()->load($sellerId, 'seller_id');
        }
        return $this->_vacationData[$sellerId];
    }

    /**
     * Get core registry
     * @return \Magento\Framework\Registry|null
     */
    protected function _getRegistry()
    {
        if (is_null($this->_coreRegistry)) {
            $this->_coreRegistry = $this->_objectManager->get(\Magento\Framework\Registry::class);
        }
        return $this->_coreRegistry;
    }

    /**
     * Retrieve customer session object.
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * Set form data
     * @return $this
     */
    protected function _setFormData($data = null)
    {
        if (null === $data) {
            $data = $this->getRequest()->getParams();
        }

        if (false === $data) {
            $this->dataPersistor->clear($this->_formSessionKey);
        } else {
            $this->dataPersistor->set($this->_formSessionKey, $data);
        }

        /* deprecated save in session */
        $this->_getSession()->setData($this->_formSessionKey, $data);

        return $this;
    }
}
