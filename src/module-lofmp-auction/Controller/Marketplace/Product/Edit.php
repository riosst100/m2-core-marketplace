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

use Lof\Auction\Model\ProductFactory;
use Lof\MarketPlace\Model\SalesFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\Auction\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Registry;
use Magento\Framework\Url;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * Lofmp\Auction\Controller\Marketplace\Product
 */
class Edit extends Action
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var SalesFactory
     */
    protected $sellerFactory;

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
     * @var Data
     */
    protected $helperData;

    /**
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     *
     * @param Context $context
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory
     * @param Url $frontendUrl
     * @param Data $helper
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SellerFactory $sellerFactory,
        Url $frontendUrl,
        Data $helper,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->helperData = $helper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->productFactory = $productFactory;
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
     * @return Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->helperData->isEnabledForSeller()) {
            $customerSession = $this->session;
            $customerId = $customerSession->getId();
            $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
            $status = $seller->getStatus();

            if ($customerSession->isLoggedIn() && $status == 1) {
                $id = $this->getRequest()->getParam('id');

                $model = $this->productFactory->create();
                $model->load($id);

                if (!$this->validate($id, $model, $seller)) {
                    return $this->_redirect('*/*');
                }

                $this->_coreRegistry->register('lofauction_product', $model);

                $title = $this->_view->getPage()->getConfig()->getTitle();
                $title->prepend(__("Catalog"));
                $title->prepend(__("Action Product"));

                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__('Auction Product'));
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

    /**
     * @param $id
     * @param $model
     * @param $seller
     * @return bool
     */
    public function validate($id, $model, $seller)
    {
        if (!!strpos($this->getRequest()->getRequestUri(), 'marketplace/lofmpauction/product/edit') && !$id) {
            $this->messageManager->addErrorMessage(__("Invalid auction id. Should be numeric value greater than 0"));
            return false;
        }
        if ($id && (!$model->getId())
        ) {
            $this->messageManager->addErrorMessage(__("This product does not exist."));
            return false;
        }

        if ($id && $model->getSellerId() != $seller->getData('seller_id')
        ) {
            $this->messageManager->addErrorMessage(__("You don\'t have permission to edit this auction"));
            return false;
        }
        return true;
    }
}
