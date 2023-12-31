<?php

namespace Lofmp\FeaturedProducts\Controller\Marketplace\Product;

use Magento\Framework\App\Action\Context;

class Chooser extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

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
     * Chooser constructor.
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Lofmp\FeaturedProducts\Model\ResourceModel\FeaturedProduct\CollectionFactory $collectionFactory
    ) {
        parent::__construct ($context);
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->session           = $customerSession;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getCustomerId();
        $status = $this->sellerFactory->create()->load($customerId,'customer_id')->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $this->layoutFactory->create();

            $uniqId = $this->getRequest()->getParam('uniq_id');
            $pagesGrid = $layout->createBlock(
                'Lofmp\FeaturedProducts\Block\Marketplace\Product\Chooser',
                '',
                ['data' => ['id' => $uniqId]]
            );

            /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setContents($pagesGrid->toHtml());
            return $resultRaw;
        } elseif($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
        } else {
            $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
            $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
