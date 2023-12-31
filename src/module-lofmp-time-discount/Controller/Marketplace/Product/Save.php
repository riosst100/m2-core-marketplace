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
namespace Lofmp\TimeDiscount\Controller\Marketplace\Product ;
use Magento\Framework\App\Action\Context;

class Save extends  \Magento\Framework\App\Action\Action
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
     * @var \Lofmp\TimeDiscount\Helper\Data
     */
    protected $_assignHelper;

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
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct ($context);
        $this->_assignHelper = $helper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * get frontend url
     *
     * @param string $route
     * @param mixed|array $params
     * @return string
     */
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
        $seller = $this->sellerFactory->create()->load($customerId,'customer_id');
        $status = $seller->getStatus();
        $data = $this->getRequest()->getPostValue();
        if ($customerSession->isLoggedIn() && $status == 1) {
            $id = $this->getRequest()->getParam('id');
            $product = $this->_objectManager->create('Lofmp\TimeDiscount\Model\Product');
             if ($id) {
                $product->load($id);
            } else {
                 $product->setTimeDiscountStatus(1);
                 $product->setStatus(0);
            }

            if(
                $id && (!$product->getId() || $product->getSellerId() != $seller->getId())
            ) {
                $this->messageManager->addError(__("This product does not exist."));
                return $this->_redirect('*/*');
            }

            $newProductId = $this->getRequest()->getParam('product_id');
            $collection = $product->getCollection()
                ->addFieldToFilter('seller_id',$seller->getId())
                ->addFieldToFilter('product_id',$newProductId);

            if ($product->getId()) {
                $collection->addFieldToFilter('id',['neq' => $product->getId()]);
            }

            if ($collection->count()) {
                $this->messageManager->addError(__("The product #%1 is already lofmptimediscount.", $newProductId));
                $this->session->setFeaturedProductFormData($this->getRequest()->getParams());
                return $this->_redirect('*/*/edit',['id' => $id]);
            }
            if (isset($data['data'])) {
                $data['data'] = json_encode($data['data']);
            }
            $product->setData($data)
                ->setId($id)
                ->setSellerId($seller->getId())
                ->save();

            $this->messageManager->addSuccess(__("The Time Discount product has been saved."));
            return $this->_redirect('*/*');
        } elseif ($customerSession->isLoggedIn() && $status == 0 ) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

}
