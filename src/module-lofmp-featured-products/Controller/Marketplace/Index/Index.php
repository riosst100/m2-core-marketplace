<?php

namespace Lofmp\FeaturedProducts\Controller\Marketplace\Index;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

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
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $collectionFactory
    ) {
        parent::__construct ($context);
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->collectionFactory = $collectionFactory;
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getCustomerId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();
        $resultPage = $this->resultPageFactory->create();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $this->deleteOutDateItem();
            $resultPage->getConfig()->getTitle()->prepend(__('Featured Products'));
            return $resultPage;
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    public function deleteOutDateItem(){
        $currentDate = date('Y-m-d');
        $sellerId = $this->marketplaceHelper->getSellerId();
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('seller_id', ['eq' => $sellerId])
            ->addFieldToFilter('featured_to', ['lt' => $currentDate]);
        foreach ($collection as $item) {
            $item->delete();
        }
    }
}
