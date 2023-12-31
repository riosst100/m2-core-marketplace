<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lofmp\SellerMembership\Block\Membership;

use Lof\MarketPlace\Model\Commission as CommissionRule;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollection;

class Index extends \Magento\Framework\View\Element\Template
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
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        /* $this->pageConfig->getTitle()->set(__('My Membership'));*/
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function processMembership()
    {
        $today = (new \DateTime())->format('Y-m-d');

        $expiryDaysBefore = $this->marketHelper->getConfig('buy_membership_page/expiry_day_before');
        if (!$expiryDaysBefore) {
            $expiryDaysBefore = 7;
        }
        $dateObj = new \DateTime();
        $dateObj->add(new \DateInterval('P' . $expiryDaysBefore . 'D'));
        //$beforeDays = $dateObj->format('Y-m-d');

        foreach ($this->membership->getCollection()->addFieldToFilter(
            'seller_id',
            $this->getSellerId()
        ) as $key => $membership) {
            if (strtotime($today) >= strtotime($membership->getData('expiration_date'))) {
                $membership->setStatus(0)->save();
                $this->getSeller()->setGroupId($this->marketHelper->getConfig('seller_settings/default_seller_group'))->save();
            }
            /*if(strtotime($beforeDays) >= strtotime($membership->getData('expiration_date'))) {
                $seller = $this->getSeller();
                $this->email->sendExpiryNotificationEmail($seller,$membership->getData('expiration_date'));
            }*/
        }
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        $seller_id = $this->marketHelper->getSellerId();
        return $seller_id;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getMembership()
    {
        $seller_id = $this->getSellerId();
        $membership = $this->membership->getCollection()->addFieldToFilter('seller_id', $seller_id)->getFirstItem();
        return $membership;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSeller()
    {
        $seller = $this->seller->getCollection()->addFieldToFilter('seller_id', $this->getSellerId())->getFirstItem();
        return $seller;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getGroup()
    {
        $group = $this->group->getCollection()
            ->addFieldToFilter('group_id', $this->getSeller()->getGroupId())
            ->getFirstItem();
        return $group;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $commission_id
     * @return array
     */
    public function lookupGroupIds($commission_id)
    {

        $connection = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('lof_marketplace_commission_group');
        $select = $connection->select('group_id')->from(
            $table
        )
            ->where(
                'commission_id = ?',
                (int)$commission_id
            );
        $groups = [];
        foreach ($connection->fetchAll($select) as $key => $commission) {
            $groups[] = $commission['group_id'];
        }
        return $groups;
    }

    /**
     * @return mixed
     */
    public function getCommission()
    {

        $commission = $this->commission->getCollection();
        foreach ($commission as $key => $_commission) {
            $groups = $this->lookupGroupIds($_commission->getId());

            if (in_array($this->getSeller()->getGroupId(), $groups)) {
                return $_commission;
            }
        }
    }

    /**
     * @return string
     */
    public function getFeeCommission()
    {
        if ($this->getCommission()) {
            $commission = $this->getCommission()->getData();
            if (is_array($commission)) {
                switch ($commission['commission_by']) {
                    case CommissionRule::COMMISSION_BY_FIXED_AMOUNT:
                        $_commission = $this->marketHelper->getPriceFomat($commission['commission_amount']) . __('fee for each sales');
                        break;
                    case CommissionRule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
                        $_commission = $commission['commission_amount'] * 100 / 100 . '% ' . __('fee for each sales');
                        break;
                }
                return $_commission;
            }
        }
    }

    /**
     * @return array
     */
    public function getOption()
    {
        $option = [];
        $group = $this->getGroup()->getData();

        if (is_array($group)) {
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

    /**
     * @param $membership
     * @return array
     */
    public function getExtraOptions($membership)
    {
        return [];
    }

    /**
     * @return Editprofile
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Membership Plans'));
        return parent::_prepareLayout();
    }
}
