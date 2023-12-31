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

namespace Lofmp\SellerMembership\Model;

class Cron
{
    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    private $membership;

    /**
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $_membershipHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Lofmp\SellerMembership\Helper\Email
     */
    protected $email;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\Url
     */
    protected $_urlBuilder;

    /**
     * Cron constructor.
     * @param \Lofmp\SellerMembership\Helper\Data $membershipHelper
     * @param Membership $membership
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lofmp\SellerMembership\Helper\Email $email
     * @param \Magento\Framework\Url $urlBuilder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Lofmp\SellerMembership\Helper\Data $membershipHelper,
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lofmp\SellerMembership\Helper\Email $email,
        \Magento\Framework\Url $urlBuilder,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->email = $email;
        $this->_membershipHelper = $membershipHelper;
        $this->helper = $helper;
        $this->seller = $seller;
        $this->membership = $membership;
        $this->_logger = $logger;
    }

    /**
     * Run process send product alerts
     *
     * @return $this
     */
    public function process()
    {
        $today = (new \DateTime())->format('Y-m-d');

        $expiryDaysBefore = $this->helper->getConfig('buy_membership_page/expiry_day_before');
        $dateObj = new \DateTime();
        $dateObj->add(new \DateInterval('P' . $expiryDaysBefore . 'D'));
        $beforeDays = $dateObj->format('Y-m-d');

        foreach ($this->membership->getCollection() as $key => $membership) {
            if (strtotime($today) >= strtotime($membership->getData('expiration_date'))) {
                $seller = $this->seller->getCollection()->addFieldToFilter(
                    'seller_id',
                    $membership->getData('seller_id')
                )->getFirstItem();
                $membership->setStatus(0)->save();
                $seller->setGroupId($this->helper->getConfig('seller_settings/default_seller_group'))->save();
            }

            if (strtotime($beforeDays) >= strtotime($membership->getData('expiration_date'))) {
                $seller = $this->seller->getCollection()->addFieldToFilter(
                    'seller_id',
                    $membership->getData('seller_id')
                )->getFirstItem();
                $this->email->sendExpiryNotificationEmail($seller, $membership->getData('expiration_date'));
            }
        }
    }
}
