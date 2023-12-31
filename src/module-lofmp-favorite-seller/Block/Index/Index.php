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
namespace Lofmp\FavoriteSeller\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'list_favorite_sellers.phtml';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    public $marketplaceHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory,
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->marketplaceHelper = $marketplaceHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Favorite Sellers'));
    }

    /**
     * @param $customerId
     * @return \Lof\MarketPlace\Model\ResourceModel\Seller\Collection
     */
    public function getFavoriteSellers($customerId)
    {
        $sellerIds = $this->getFavoriteSellerIds($customerId);
        if(!$sellerIds) return null;

        $sellerCollection = $this->sellerCollectionFactory->create();
        $sellerSet = $sellerCollection->addFieldToFilter('seller_id', ['IN' => $sellerIds])->load();
        return $sellerSet;
    }

    /**
     * @param $customerId
     * @return array|bool
     */
    public function getFavoriteSellerIds($customerId){
        if (!$customerId) {
            return false;
        }

        $subscriptionCollection = $this->subscriptionCollectionFactory->create();
        $sellerSet = $subscriptionCollection->addFieldToFilter('customer_id', $customerId)
                                ->addFieldToSelect('seller_id')
                                ->load();
        $sellerIds = [];
        foreach($sellerSet as $seller){
            $sellerIds[] = $seller->getSellerId();
        }
        return count($sellerIds) != 0 ? $sellerIds : false;
    }

    /**
     * @return bool
     */
    public function checkCustomerLoggedIn(){
        return (bool)$this->customerSession->isLoggedIn();
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * @return \Lof\MarketPlace\Helper\Data
     */
    public function getMarketPlaceHelper(){
        return $this->marketplaceHelper;
    }

    /**
     * @param $sellerId
     * @return string
     */
    public function getDeleteUrl($sellerId){
        return $this->_urlBuilder->getUrl('favoriteseller/action/delete', ['id' => $sellerId]);
    }

}
