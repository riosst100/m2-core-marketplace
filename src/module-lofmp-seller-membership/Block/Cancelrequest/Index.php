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
 * @package    Lof_CustomerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Block\Cancelrequest;

use Lofmp\SellerMembership\Helper\Data as HelperData;

//use Lofmp\SellerMembership\Model\MembershipFactory;
use Lofmp\SellerMembership\Model\CancelrequestFactory;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CancelrequestFactory
     */
    protected $_cancelrequestFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    public $customerSessionFactory;

    /**
     * @var \Lofmp\SellerMembership\Model\Transaction
     */
    protected $transaction;

    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * @var \Lofmp\SellerMembership\Model\MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var \Lofmp\SellerMembership\Model\Group
     */
    protected $group;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Grid\Collection
     */
    protected $catalogRule;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\Quote\Collection
     */
    protected $rule;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var null
     */
    protected $_current_membership = null;

    /**
     * @var Object|mixed|string|null
     */
    protected $_membership = null;

    /**
     * @var Object|null
     */
    protected $_customer = null;

    /**
     * @var null
     */
    protected $_cancel_request_list = null;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $_coreSession;

    /**
     * @var  \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_date;

   /**
     * @var \Lofmp\SellerMembership\Helper\Membership $membershipHelper
     */
    protected $membershipHelper;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Lofmp\SellerMembership\Model\Transaction $transaction
     * @param \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory
     * @param \Lofmp\SellerMembership\Model\Membership $membership
     * @param \Lof\MarketPlace\Model\Group $group
     * @param HelperData $helper
     * @param CancelrequestFactory $cancelrequestFactory
     * @param \Magento\CatalogRule\Model\ResourceModel\Grid\Collection $catalogRule
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Quote\Collection $rule
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $coreSession
     * @param \Lofmp\SellerMembership\Helper\Membership $membershipHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Lofmp\SellerMembership\Model\Transaction $transaction,
        \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory,
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Lof\MarketPlace\Model\Group $group,
        \Lofmp\SellerMembership\Helper\Data $helper,
        CancelrequestFactory $cancelrequestFactory,
        \Magento\CatalogRule\Model\ResourceModel\Grid\Collection $catalogRule,
        \Magento\SalesRule\Model\ResourceModel\Rule\Quote\Collection $rule,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Lofmp\SellerMembership\Helper\Membership $membershipHelper,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->rule = $rule;
        $this->_date = $date;
        $this->catalogRule = $catalogRule;
        $this->group = $group;
        $this->membership = $membership;
        $this->_membershipFactory = $membershipFactory;
        $this->transaction = $transaction;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->priceCurrency = $priceCurrency;
        $this->_productFactory = $productFactory;
        $this->_cancelrequestFactory = $cancelrequestFactory;
        $this->_coreSession = $coreSession;
        $this->membershipHelper = $membershipHelper;
        parent::__construct($context, $data);
    }

     /**
     * Preparing global layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Cancel Request'));
    }

    /**
     * @return mixed
     */
    public function getCurrentMembership()
    {
        if (!$this->_current_membership) {
            $this->_current_membership = $this->membershipHelper->getCurrentMembership();
        }

        return $this->_current_membership;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = $this->customerSessionFactory->create()->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getMembership()
    {
        if (!isset($this->_membership)) {
            $this->_membership = $this->membershipHelper->getMembership();
        }
        return $this->_membership;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getTransactionCollection()
    {
        if (!isset($this->_transactionCollection)) {
            $this->_transactionCollection = $this->transaction->getCollection()->addFieldToFilter('main_table.customer_id',
                $this->getCustomer()->getId());
        }
        return $this->_transactionCollection;
    }

    /**
     * @param int $price
     * @return string
     */
    public function formatPrice($price = 0)
    {
        $price = $this->priceCurrency->convert($price);
        return $this->priceCurrency->format($price, false);
    }

    /**
     * Format base currency
     * @param number $price
     */
    public function formatBasePrice($price = 0)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($price, 2, [], false);
    }

    /**
     * Is using base currency
     * @return boolean
     */
    public function isBaseCurrency()
    {
        $store = $this->_storeManager->getStore();
        return $store->getBaseCurrencyCode() == $store->getCurrentCurrencyCode();
    }

    /**
     * Get buy membership URL
     * @return string
     */
    public function getBuyMembershipUrl()
    {
        $route = $this->helper->getConfig("buy_membership_page/route");
        if (!$route) {
            $route = 'lofcustomermembership/buy/';
        }
        return $this->getUrl($route);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getGroup()
    {
        $membershipCollection = $this->getMembership();
        $group_id = 0;
        if ($membershipCollection->count()) {
            $group_id = $membershipCollection->getFirstItem()->getData('group_id');
        }
        $group = $this->group->getCollection()->addFieldToFilter('group_id', $group_id)->getFirstItem();
        return $group;
    }

    /**
     * @return array
     */
    public function getOption()
    {
        $option = [];
        $group_id = $this->getGroup()->getData('group_id');
        foreach ($this->catalogRule as $key => $catalogRule) {
            if (in_array($group_id, $catalogRule->getData('customer_group_ids'))) {
                $option[] = $catalogRule->getData('name');
            }
        }
        foreach ($this->rule as $key => $rule) {
            if (in_array($group_id, $rule->getData('customer_group_ids'))) {
                $option[] = $rule->getData('name');
            }
        }
        return $option;
    }

    /**
     * @return string
     */
    public function getSubscriptionDescription()
    {
        $membershipCollection = $this->getMembership();
        $product_description = "";
        if ($membershipCollection) {
            $membership = $membershipCollection->getFirstItem();
            $product_id = $membership->getProductId();
            if ($product_id) {
                $product = $this->_productFactory->create()->load($product_id);
                if ($product->getId()) {
                    $product_description = $product->getDescription();
                }
            }
        }
        return $product_description;
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('lofcustomermembership/cancelrequest');
    }

    /**
     * @return bool|int
     */
    public function infoCancelrequest()
    {
        if ($this->_coreSession->getData('cancelrequest') == 1) {
            return 1;
        }
        if ($this->_coreSession->getData('cancelrequest') == 2) {
            return 2;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function sussessCancelrequest()
    {
        if ($this->_coreSession->getData('cancelrequest') == 1) {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function errorCancelrequest()
    {
        if ($this->_coreSession->getData('cancelrequest') == 2) {
            return true;
        }
    }

    /**
     * @return string
     */
    public function messageCancelrequest()
    {
        if ($this->_coreSession->getData('cancelrequest_message')) {
            return $this->_coreSession->getData('cancelrequest_message');
        }
        return '';
    }

    public function clearCancelrequest()
    {
        $this->_coreSession->setData('cancelrequest', 0);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getCancelRequestPending()
    {
        $cancelrequest_collection = null;
        $membership = $this->getCurrentMembership();
        if ($membership) {
            $membership_id = $membership->getData('membership_id');
            $cancelrequest_collection = $this->_cancelrequestFactory->create()->getCollection()
                ->addFieldToFilter('membership_id', $membership_id)
                ->addFieldToFilter('status', \Lofmp\SellerMembership\Model\Cancelrequest::PENDING);
        }

        return $cancelrequest_collection;
    }

    /**
     * @return bool|\Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getCancelRequest()
    {
        if (!isset($this->_cancel_request_list)) {
            $this->_cancel_request_list = false;

            $membership = $this->getCurrentMembership();
            if ($membership) {
                $membership_id = $membership->getData('membership_id');
                $this->_cancel_request_list = $this->_cancelrequestFactory->create()->getCollection()
                    ->addFieldToFilter('membership_id', $membership_id);
            }
        }
        return $this->_cancel_request_list;
    }
}
