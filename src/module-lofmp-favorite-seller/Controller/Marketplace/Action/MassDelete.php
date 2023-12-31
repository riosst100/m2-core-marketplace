<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Controller\Marketplace\Action;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class MassDelete extends Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \Magento\Framework\Url
     */
    protected $frontendUrl;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var
     */
    protected $_actionFlag;

    /**
     * @var \Lofmp\FavoriteSeller\Helper\ConfigData $configData
     */
    protected $configData;

    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Lofmp\FavoriteSeller\Helper\ConfigData $configData
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Lofmp\FavoriteSeller\Helper\ConfigData $configData
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->filter = $filter;
        $this->sellerFactory = $sellerFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->frontendUrl = $frontendUrl;
        $this->actionFlag = $context->getActionFlag();
        $this->resultPageFactory = $resultPageFactory;
        $this->configData = $configData;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getFrontendUrl($route = '', $params = []){
        return $this->frontendUrl->getUrl($route,$params);
    }

    /**
     * @param $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url){
        $this->getResponse()->setRedirect($url);
        $this->customerSession->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->configData->isEnable()) {
            $sellerSession = $this->customerSession;
            $sellerId = $this->marketplaceHelper->getSellerId();

            $status = $this->sellerFactory->create()->load($sellerId,'seller_id')->getStatus();

            if ($sellerSession->isLoggedIn() && $status == 1) {
                $collection = $this->filter->getCollection($this->subscriptionCollectionFactory->create());
                $collectionSize = $collection->getSize();

                foreach ($collection as $subscription) {
                    $subscription->delete();
                }

            } elseif($sellerSession->isLoggedIn() && $status == 0) {
                $this->_redirectUrl ( $this->getFrontendUrl('lofmarketplace/seller/becomeseller') );
            } else {
                $this->messageManager->addNotice(__( 'You must have a seller account to access' ) );
                $this->_redirectUrl ($this->getFrontendUrl('lofmarketplace/seller/login'));
            }

            return $this->_redirect('favoriteseller/index/index/');
        } else {
            $this->messageManager->addNotice(__('You dont have permission to access this feature.'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/catalog/dashboard/'));
        }
    }
}
