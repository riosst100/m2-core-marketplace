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
 * @copyright  Copyright (c) 2022 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Marketplace;

use Magento\Customer\Model\Session;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * AbstractMarketplaceAction post controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractMarketplaceAction extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';
    const SELLER_RESOURCE = 'Lof_MarketPlace::marketplace';

    /**
     * Seller state const
     */
    const STATE_NOT_LOGGED_IN = "not_loggin";
    const STATE_APPROVED = "approved";
    const STATE_NEED_APPROVAL = "need_approval";

    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var ProductFactory
     */
    private $auctionFactory;

    /**
     * @var Url
     */
    protected $frontendUrl;

    /**
     * @var ActionFlag
     */
    protected $actionFlag;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var object
     */
    protected $collectionFactory;

    /**
     * @var object|mixed|null
     */
    protected $currentSeller = null;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param Url $frontendUrl
     * @param SellerFactory $sellerFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Filter $filter,
        Url $frontendUrl,
        SellerFactory $sellerFactory
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->customerUrl = $customerUrl;
        $this->filter = $filter;
        $this->frontendUrl = $frontendUrl;
        $this->sellerFactory = $sellerFactory;
        $this->actionFlag = $context->getActionFlag();
    }

    /**
     * Retrieve customer session object.
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->frontendUrl->getUrl($route, $params);
    }
    /**
     * Redirect to URL
     * @param string $url
     * @return ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->customerSession->setIsUrlNotice($this->actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $urlModel = $this->customerUrl;
        $loginUrl = $urlModel->getLoginUrl();
        if (!$this->customerSession->authenticate($loginUrl)) {
            $this->actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * get current seller
     *
     * @return Lof\MarketPlace\Model\Seller|null
     */
    protected function getCurrentSeller()
    {
        if(!$this->currentSeller && $this->_getSession()->isLoggedIn()){
            $customerId = $this->_getSession()->getCustomerId();
            $this->currentSeller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        }
        return $this->currentSeller;
    }

    /**
     * Get seller state
     *
     * @return string
     */
    protected function getSellerState()
    {
        $seller = $this->getCurrentSeller();
        if($seller && $seller->getId()){
            return 1 == $seller->getStatus()?self::STATE_APPROVED:self::STATE_NEED_APPROVAL;
        }
        return self::STATE_NOT_LOGGED_IN;
    }

    /**
     * @param int $modelSellerId
     * @param string|null $actionName
     * @return bool
     */
    public function validate($modelSellerId, $actionName = null)
    {
        $sellerId = $this->helper->getSellerId();
        if ($modelSellerId != $sellerId) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to access the action'));
            return false;
        } else {
            return true;
        }
    }

    /**
     * check current seller is active or not
     *
     * @param bool|int $checkPermission
     * @return bool|int
     */
    public function isActiveSeler($checkPermission = false)
    {
        if (!$this->isEnabledModule()) {
            $this->messageManager->addErrorMessage(__('You don\'t have permission to access the action'));
            $this->_redirectUrl($this->getFrontendUrl('marketplace/catalog/dashboard'));
            return false;
        }
        if ($checkPermission && !$this->allowSellerManageFeature()) {
            $this->messageManager->addErrorMessage('You dont have permission to access the feature.');
            $this->_redirectUrl($this->getFrontendUrl('marketplace/catalog/dashboard'));
            return false;
        }
        $sellerState = $this->getSellerState();
        switch($sellerState){
            case "approved":
                return true;
                break;
            case "not_loggin":
                $this->messageManager->addNotice(__('You must have a seller account to access'));
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
                return false;
                break;
            case "need_approval":
            default:
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
                return false;
                break;
        }
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|bool
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function executeMassAction()
    {
        try {
            $collection = $this->collectionFactory ? $this->filter->getCollection($this->collectionFactory->create()) : null;
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_redirectUrl($this->getFrontendUrl('marketplace/catalog/dashboard'));
            return false;
        }
    }

    /**
     * Check is enabled module on seller dashboard
     *
     * @return bool|int
     */
    public function isEnabledModule()
    {
        return true;
    }

    /**
     * Check is enabled module on seller dashboard
     *
     * @return bool|int
     */
    protected function allowSellerManageFeature()
    {
        return true;
    }

    /**
     * Set status to collection items
     *
     * @param \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|bool
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        return false;
    }
}
