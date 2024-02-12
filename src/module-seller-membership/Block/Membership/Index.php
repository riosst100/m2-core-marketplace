<?php

namespace CoreMarketplace\SellerMembership\Block\Membership;

use Lof\MarketPlace\Model\Commission as CommissionRule;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;

class Index extends \Lofmp\SellerMembership\Block\Membership\Index
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    /**
     * @var
     */
    protected $_itemsCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $_productList;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketHelper;

    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * @var CommissionRule
     */
    protected $commission;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $group;

    /**
     * @var \Lofmp\SellerMembership\Helper\Email
     */
    protected $email;

    /**
     * @var \Magento\Framework\Url
     */
    protected $urlHelper;

    /**
     * Index constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Helper\Data $marketHelper
     * @param CommissionRule $commission
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\Group $group
     * @param \Lofmp\SellerMembership\Model\Membership $membership
     * @param \Lofmp\SellerMembership\Helper\Email $email
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param ProductCollection $productCollectionFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Url $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        \Lof\MarketPlace\Model\Commission $commission,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Group $group,
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Lofmp\SellerMembership\Helper\Email $email,
        \Magento\Framework\App\ResourceConnection $resource,
        ProductCollection $productCollectionFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Url $urlHelper,
        array $data = []
    ) {
        $this->_storeManager = $context->getStoreManager();
        $this->group = $group;
        $this->seller = $seller;
        $this->commission = $commission;
        $this->membership = $membership;
        $this->_resource = $resource;
        $this->_customerSession = $customerSession;
        $this->_productCollection = $productCollectionFactory;
        $this->marketHelper = $marketHelper;
        $this->_logger = $logger;
        $this->email = $email;
        $this->urlHelper = $urlHelper;
        parent::__construct(
            $context,
            $customerSession,
            $marketHelper,
            $commission,
            $seller,
            $group,
            $membership,
            $email,
            $resource,
            $productCollectionFactory,
            $logger,
            $data
        );
    }

    public function getFrontendUrl($urlPath) 
    {
        $storeId = $this->_storeManager->getStore()->getId();
        
        return $this->urlHelper->getUrl($urlPath, [ '_scope' => $storeId]);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getMembership()
    {
        $seller_id = $this->getSellerId();
        $membershipColl = $this->membership->getCollection()->addFieldToFilter('seller_id', $seller_id);
        if ($membershipColl->getSize()) {
            return $membershipColl->getFirstItem();
        }

        return null;
    }

    /**
     * @return array
     */
    public function getOption()
    {
        $option = [];
        $group = $this->getGroup()->getData();

        if ($group && is_array($group)) {
            if ($group['can_add_product'] == 1) {
                $option[] = __('Can add product');
            }
            if ($group['can_cancel_order'] == 1) {
                $option[] = __('Can cancel order');
            }
            if ($group['can_create_invoice'] == 1) {
                $option[] = __('Can create invoice');
            }
            if ($group['can_create_shipment'] == 1) {
                $option[] = __('Can create shipment');
            }
            if ($group['hide_payment_info'] == 1) {
                $option[] = __('Hide payment info');
            }
            if ($group['hide_customer_email'] == 1) {
                $option[] = __('Hide customer email');
            }
            if ($group['can_use_shipping'] == 1) {
                $option[] = __('Can Use Shipping');
            }
            if ($group['can_submit_order_comments'] == 1) {
                $option[] = __('Can submit order comments');
            }
            if ($group['can_use_message'] == 1) {
                $option[] = __('Can use message');
            }
            if ($group['can_use_review'] == 1) {
                $option[] = __('Can use review');
            }
            if ($group['can_use_rating'] == 1) {
                $option[] = __('Can use rating');
            }
            if ($group['can_use_import_export'] == 1) {
                $option[] = __('Can import/export product');
            }
            if ($group['can_use_vacation'] == 1) {
                $option[] = __('Can use vacation');
            }
            if ($group['can_use_report'] == 1) {
                $option[] = __('Can use report');
            }
            if ($group['can_use_withdrawal'] == 1) {
                $option[] = __('Can use withdrawal');
            }

            return $option;
        }
    }
}
