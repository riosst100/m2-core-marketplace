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
namespace Lofmp\FavoriteSeller\Block\Seller;

class View extends \Magento\Framework\View\Element\Template {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
     /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;


    protected $subscriptionCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
    */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session  $customerSession,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        array $data = []
        ) {

        $this->_helper        = $helper;
        $this->_coreRegistry  = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource      = $resource;
        $this->orderitems     = $orderitems;
        $this->customerSession = $customerSession;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        parent::__construct($context);
    }
    /**
     *  get Seller Colection
     *
     * @return Object
     */
     public function getSellerCollection(){
        $store            = $this->_storeManager->getStore();
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }

    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }

    /**
     * @return int|null
     */
    public function getSellerId() {
        $sellerId = $this->getCurrentSeller()->getData('seller_id');
        return $sellerId;
    }

    /**
     * @return bool
     */
    public function checkCustomerLoggedIn(){
        return (bool)$this->customerSession->isLoggedIn();
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * @return bool
     */
    public function isSubscribed(){
        $sellerId = $this->getSellerId() ? $this->getSellerId() : 0;
        $customerId = $this->getCustomerId();

        $subscriptionCollection = $this->subscriptionCollectionFactory->create();
        $subscriptionSet = $subscriptionCollection->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('seller_id', $sellerId)
            ->load();

        return $subscriptionSet->count() != 0 ? true : false;
    }

    /**
     * @return string|void
     */
    public function _toHtml(){
        if(!$this->_helper->getConfig('product_view_page/enable_seller_info')) return;
        return parent::_toHtml();
    }
}
