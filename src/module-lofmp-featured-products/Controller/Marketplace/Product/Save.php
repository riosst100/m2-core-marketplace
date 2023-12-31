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
 * @package    Lofmp_FeaturedProducts
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\FeaturedProducts\Controller\Marketplace\Product;

use Magento\Framework\App\Action\Context;

class Save extends \Magento\Framework\App\Action\Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Lofmp\FeaturedProducts\Model\FeaturedProductFactory
     */
    protected $featuredProductFactory;

    /**
     * @var \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory
     */
    protected $featuredProductCollectionFactory;

    /**
     * @param Context $context
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Lofmp\FeaturedProducts\Model\FeaturedProductFactory $featuredProductFactory
     * @param \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $featuredProductCollectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Lofmp\FeaturedProducts\Model\FeaturedProductFactory $featuredProductFactory,
        \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $featuredProductCollectionFactory
    ) {
        parent::__construct ($context);
        $this->_actionFlag = $context->getActionFlag();
        $this->_frontendUrl = $frontendUrl;
        $this->coreRegistry = $registry;
        $this->session = $customerSession;
        $this->productFactory = $productFactory;
        $this->sellerFactory     = $sellerFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->featuredProductFactory = $featuredProductFactory;
        $this->featuredProductCollectionFactory = $featuredProductCollectionFactory;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getFrontendUrl($route = '', $params = []){
        return $this->_frontendUrl->getUrl($route,$params);
    }

    /**
     * @param $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getCustomerId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $requestData = $this->getRequest()->getParams();
            if(isset($requestData['product_id'])){
                $productId = $requestData['product_id'];
                $featuredFrom = $requestData['featured_from'];
                $featuredTo = $requestData['featured_to'];
                $sortOrder = $requestData['sort_order'];
                $sortOrder = $sortOrder?(int)$sortOrder:0;
                if($productId == "" || !$productId){
                    $this->sendError(__("The product Id is required, please choose product before!"));
                    $this->_redirect('featuredproducts/index');
                }else {
                    $sellerId = $this->marketplaceHelper->getSellerId();
                    $product = $this->productFactory->create()->load($productId);
                    if($product->getSellerId() != $sellerId)
                        $this->sendError(__('Product not exists'));

                    $featuredProductCollection = $this->featuredProductCollectionFactory->create()
                        ->addFieldToFilter('product_id', ['eq' => $productId]);
                    if(count($featuredProductCollection) != 0)
                        $this->sendError(__('This product already is a featured product'));

                    $this->featuredProductFactory->create()
                        ->setProductId($productId)
                        ->setSellerId($sellerId)
                        ->setFeaturedFrom($featuredFrom)
                        ->setFeaturedTo($featuredTo)
                        ->setSortOrder($sortOrder)
                        ->save();

                    $this->messageManager->addSuccess(__( 'Add Featured Product Successful' ) );
                    $this->_redirect('featuredproducts/index');
                }
            } else {
                $id = $requestData['id'];
                $featuredFrom = $requestData['featured_from'];
                $featuredTo = $requestData['featured_to'];
                $sortOrder = $requestData['sort_order'];
                $sortOrder = $sortOrder?(int)$sortOrder:0;

                if(!$id){
                    $this->sendError(__("The product Id is required, please choose product before!"));
                    $this->_redirect('featuredproducts/index');
                } else {
                    $sellerId = $this->marketplaceHelper->getSellerId();
                    $featuredProduct = $this->featuredProductFactory->create()->load($id);

                    if ($featuredProduct->getSellerId() != $sellerId) {
                        $this->sendError(__('Product not exists'));
                    }

                    $featuredProduct->setFeaturedFrom($featuredFrom);
                    $featuredProduct->setFeaturedTo($featuredTo);
                    $featuredProduct->setSortOrder($sortOrder);
                    $featuredProduct->save();
                    $this->messageManager->addSuccess(__('Edit Featured Product Successful'));
                    $this->_redirect('featuredproducts/index');
                }
            }
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * @param $message
     */
    public function sendError($message){
        $this->messageManager->addError(__( $message ) );
        $this->_redirect('featuredproducts/product/add');
    }
}
