<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\TimeDiscount\Controller\Marketplace\Product;
use Magento\Framework\App\Action\Context;

class Edit extends  \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\SalesFactory
     */
    protected $sellerFactory;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

     /**
     * @var \Lof\TimeDiscount\Helper\Data
     */
    protected $_assignHelper;

    protected $_coreRegistry;
    /**
     *
     * @param Context $context
     * @param Magento\Framework\App\Action\Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Lofmp\TimeDiscount\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {

        parent::__construct ($context,$coreRegistry);
        $this->_coreRegistry = $coreRegistry;
        $this->_assignHelper = $helper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route,$params);
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
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $id = $this->getRequest()->getParam('id');
            $product = $this->_objectManager->create('Lofmp\TimeDiscount\Model\Product');
            $product->load($id);

            if(
                $id && (!$product->getId())
            ) {
                $this->messageManager->addError(__("This product does not exist."));
                return $this->_redirect('*/*');
            }
           /* if($data = $this->_session->getFeaturedProductFormData(true)){
                $product->setData($data);
            }*/
            $this->_coreRegistry->register('current_product', $product);
            $this->_coreRegistry->register('product', $product);

            //$this->_initAction();
            $title = $this->_view->getPage()->getConfig()->getTitle();
            $title->prepend(__("Catalog"));
            $title->prepend(__("Action Product"));
            /*$this->_addBreadcrumb(__("Catalog"), __("Catalog"))->_addBreadcrumb(__("Featured Products"), __("Featured Products"));

            if($product->getId()){
                $title->prepend($product->getName());
                $this->_addBreadcrumb($product->getName(), $product->getName());
            }else{
                $this->_addBreadcrumb(__("New"), __("New"));
                $title->prepend(__("New Featured Products"));
            }*/

            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('TimeDiscount Product'));
            return $resultPage;
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

}
