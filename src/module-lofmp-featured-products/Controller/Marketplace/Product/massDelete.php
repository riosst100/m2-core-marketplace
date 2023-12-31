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

class MassDelete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;

    /**
     *
     */
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $collectionFactory
    ) {
        parent::__construct ($context);
        $this->session = $customerSession;
        $this->_frontendUrl = $frontendUrl;
        $this->resultPageFactory = $resultPageFactory;
        $this->sellerFactory     = $sellerFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->collectionFactory = $collectionFactory;
        $this->_actionFlag = $context->getActionFlag();
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
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getCustomerId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $sellerId = $this->marketplaceHelper->getSellerId();
            $collection = $this->collectionFactory->create();
            $selectedIds = $this->getRequest()->getParam('selected');
            $recordDeleted = 0;
            $x= $this->getRequest()->getParams();
            $collection->addFieldToFilter('id', ['in' => $selectedIds])
                ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
            $collectionItems = $collection->getItems();

            foreach ($collectionItems as $item) {
                $item->delete();
                $recordDeleted++;
            }

            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been deleted.', $recordDeleted)
            );

            return $resultRedirect->setPath('*/index/index');

        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
