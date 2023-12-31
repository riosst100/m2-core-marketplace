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

namespace Lofmp\SellerMembership\Helper;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Lofmp\SellerMembership\Model\Membership as ModelMembership;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\ResourceModel\Seller as SellerResource;

class Membership extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    public $customerSessionFactory;

    /**
     * @var \Lofmp\SellerMembership\Model\MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var \Lofmp\SellerMembership\Model\Group
     */
    protected $group;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Object|mixed|null
     */
    protected $_current_membership = null;

    /**
     * @var \\Lofmp\SellerMembership\Model\ResourceModel\Membership\Collection|mixed|string|null
     */
    protected $_membershipCollection = null;

    /**
     * @var int|null
     */
    protected $_customerId = 0;

    /**
     * @var Object|null
     */
    protected $_customer = null;

    /**
     * @var Seller|null
     */
    protected $_seller = null;

    /**
     * @var array|mixed|null
     */
    protected $_otherMemberships = [];

    /**
     * @var int
     */
    protected $sellerId = 0;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var SellerResource
     */
    protected $sellerResource;

    /**
     * constract membership helper
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param TimezoneInterface $timezoneInterface
     * @param Data $helperData
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param SellerResource $sellerResource
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        TimezoneInterface $timezoneInterface,
        Data $helperData,
        SellerCollectionFactory $sellerCollectionFactory,
        SellerResource $sellerResource
    ) {
        $this->customerSessionFactory = $customerSessionFactory;
        $this->membershipFactory = $membershipFactory;
        $this->dateTime = $dateTime;
        $this->timezoneInterface = $timezoneInterface;
        $this->helperData = $helperData;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->sellerResource = $sellerResource;
        parent::__construct($context);
    }

    /**
     * Get date time
     *
     * @return
     */
    public function getDateTime(){
        return $this->dateTime;
    }

    /**
     * get timzone date time
     *
     * @param string
     * @return string
     */
    public function getTimezoneDateTime($dateTime = "today"){
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->dateTime->gmtDate();
        }

        $today = $this->timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * @return mixed
     */
    public function getCurrentMembership()
    {
        if (!$this->_current_membership) {
            $this->processMembership();
            $membershipCollection = $this->getMembership();
            if ($membershipCollection->count()) {
                $this->_current_membership = $membershipCollection->getFirstItem();
            }
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
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSeller()
    {
        if (!$this->_seller && $this->getSellerId()) {
            $this->_seller = $this->getSellerById($this->getSellerId());
        }
        return $this->_seller;
    }

    /**
     * Set seller id
     *
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id)
    {
        $this->sellerId = $seller_id;
        return $this;
    }

    /**
     * get current customer id
     * @return int
     */
    public function getCustomerId()
    {
        if (!$this->_customerId) {
            /** @var \Magento\Customer\Model\Customer $customer  */
            $customer = $this->getCustomer();
            $this->_customerId = $customer ? $customer->getId() : 0;
        }
        return $this->_customerId;
    }

    /**
     * get current seller id
     * @return int
     */
    public function getSellerId()
    {
        if (!$this->sellerId && $this->getCustomerId()) {
            $seller = $this->getSellerByCustomer($this->getCustomerId());
            $this->sellerId = $seller && $seller->getId() ? $seller->getId() : 0;
        }
        return $this->sellerId;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getMembership()
    {
        if (!isset($this->_membershipCollection)) {
            $this->_membershipCollection = $this->membershipFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('seller_id', $this->getSellerId());

            $this->_membershipCollection->addFieldToFilter(
                ['status', 'status'],
                [
                    ['eq' => 1],
                    ['eq' => 2]
                ]
            )->setOrder("membership_id", "DESC");
        }
        return $this->_membershipCollection;
    }

    /**
     * @return array|mixed|null
     */
    public function getOtherMemberships()
    {
        if (!isset($this->_otherMemberships)) {
            $sellerId = $this->getSellerId(); //$this->customerSessionFactory->create()->getCustomerId();
            $currentMembership = $this->getCurrentMembership();
            if ($currentMembership && $sellerId) {
                $collection = $this->membershipFactory->create()->getCollection()->addFieldToFilter('seller_id', $sellerId);
                $collection->addFieldToFilter(
                    ['status', 'status'],
                    [
                        ['eq' => 1],
                        ['eq' => 2]
                    ]
                )
                ->addFieldToFilter('membership_id', ['neq' => $currentMembership->getMembershipId()])
                ->setOrder("membership_id", "DESC");

                if ($collection->count()) {
                    $this->_otherMemberships = $collection;
                }
            }
        }
        return $this->_otherMemberships;
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function processMembership()
    {
        if ($seller = $this->getSeller()) {
            $today = $this->getTimezoneDateTime();
            $expiryDaysBefore = $this->helperData->getConfig('buy_membership_page/expiry_day_before');
            if (!$expiryDaysBefore) {
                $expiryDaysBefore = 7;
            }
            $dateObj = new \DateTime();
            $dateObj->add(new \DateInterval('P' . $expiryDaysBefore . 'D'));
            //$beforeDays = $dateObj->format('Y-m-d');

            $collection = $this->membershipFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('seller_id', (int)$seller->getId());

            if ($collection->count() > 0) {
                foreach ($collection as $membership) {
                    if (strtotime($today) >= strtotime($membership->getData('expiration_date'))) {
                        $before_seller_group_id = $membership->getBeforeSellerGroupId();
                        $before_seller_group_id = $before_seller_group_id ? (int)$before_seller_group_id: (int)$this->helperData->getConfig('seller_settings/default_seller_group');
                        $membership
                            ->setStatus(ModelMembership::DISABLE)
                            ->setBeforeSellerGroupId($before_seller_group_id)
                            ->save();

                        /** @var Seller $seller */
                        $seller->setGroupId($before_seller_group_id);
                        $this->sellerResource->save($seller);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * get seller by customer id
     *
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByCustomer($customerId)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('customer_id', ['eq' => $customerId])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();

        $this->_seller = $seller;
        return $this->_seller;
    }

    /**
     * get seller by seller id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerById($sellerId)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['eq' => $sellerId])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();

        $this->_seller = $seller;
        return $this->_seller;
    }
}
