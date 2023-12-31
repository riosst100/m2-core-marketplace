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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Helper;

use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\OrderFactory;
use Lof\MarketPlace\Model\RatingFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filter\FilterManager;

class Rating extends \Magento\Framework\App\Helper\AbstractHelper
{
    const COMPLATE_STATE = "complete";

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var OrderFactory
     */
    protected $sellerOrderFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @var RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var bool|null
     */
    protected $verifiedBuyer = null;

    /**
     * Seller constructor.
     * @param Context $context
     * @param SellerFactory $sellerFactory
     * @param OrderFactory $sellerOrderFactory
     * @param CustomerFactory $customerFactory
     * @param Data $helperData
     * @param Session $customerSession
     * @param FilterManager $filter
     * @param RatingFactory $ratingFactory
     */
    public function __construct(
        Context $context,
        SellerFactory $sellerFactory,
        OrderFactory $sellerOrderFactory,
        CustomerFactory $customerFactory,
        Data $helperData,
        Session $customerSession,
        FilterManager $filter,
        RatingFactory $ratingFactory
    ) {
        parent::__construct($context);
        $this->sellerFactory = $sellerFactory;
        $this->sellerOrderFactory = $sellerOrderFactory;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->filter = $filter;
        $this->ratingFactory = $ratingFactory;
    }

    /**
     * Check allow guest/customer rating for this seller
     *
     * @param int $sellerId
     * @param string $customerEmail
     * @param int $customerId
     * @return bool
     */
    public function checkAllowRating($sellerId, $customerEmail = "", $customerId = 0)
    {
        if (!$this->helperData->getConfig("general_settings/enable_rating")) {
            return false;
        }
        $flag = true;

        $limit_number_of_rating = (int)$this->helperData->getConfig("general_settings/limit_number_of_rating");

        $require_loggin = (int)$this->helperData->getConfig("general_settings/require_loggin");
        $isLoggedIn = ( $this->helperData->isLoggedIn() || ($customerId > 0 && !empty($customerEmail)) )? true : false;
        if ($isLoggedIn && !$customerEmail && !$customerId) {
            $customerId = $this->customerSession->getId();
            $customerEmail = $this->customerSession->getCustomer()->getEmail();
        }

        if (!$customerEmail || ($require_loggin && !$isLoggedIn)) {
            $flag = false;
        } else {
            $checkedLimitFlag = $this->checkLimitted($sellerId, $limit_number_of_rating, $customerId, $customerEmail);
            if ($this->verifiedBuyer !== null && $this->verifiedBuyer) {
                $checkedPurchasedFlag = true;
            } else {
                $checkedPurchasedFlag = $this->checkPurchasedOrder($sellerId, $customerId, $customerEmail);
            }

            $flag = $checkedLimitFlag && $checkedPurchasedFlag ? true : false;
        }
        return $flag;
    }

    /**
     * Set verified buyer
     *
     * @param bool $flag
     * @return $this
     */
    public function setVerifiedBuyer($flag = false)
    {
        $this->verifiedBuyer = $flag;
        return $this;
    }
    /**
     * Check rating limited or not
     *
     * @param int $sellerId
     * @param int $limit
     * @param int $customerId
     * @param string $customerEmail
     * @param bool
     */
    public function checkLimitted($sellerId, $limit, $customerId, $customerEmail)
    {
        if (!$limit) {
            return true;
        }
        $ratingCollection = $this->ratingFactory->create()->getCollection()
                                ->addFieldToFilter("seller_id", (int)$sellerId);
        $totalRatings = 0;
        if ($customerId) {
            $totalRatings = $ratingCollection->addFieldToFilter("customer_id", $customerId)->getSize();
        } else {
            $totalRatings = $ratingCollection->addFieldToFilter("email", $customerEmail)->getSize();
        }
        if ($totalRatings < $limit) {
            return true;
        }
        return false;
    }

    /**
     * Check rating require purchased order, completed orders or not
     *
     * @param int $sellerId
     * @param int $customerId
     * @param string $customerEmail
     * @return bool
     */
    public function checkPurchasedOrder( $sellerId, $customerId, $customerEmail = "")
    {
        $require_purchased = (int)$this->helperData->getConfig("general_settings/require_purchased");
        $require_order_completed = (int)$this->helperData->getConfig("general_settings/require_order_completed");

        if (!$require_purchased) {
            return true;
        }
        $orderCollection = $this->sellerOrderFactory->create()->getCollection()
                                ->addFieldToFilter("main_table.seller_id", (int)$sellerId);
        $orderCollection->getSelect()
                ->join(
                    ['order_table' => $orderCollection->getResource()->getTable("sales_order")],
                    'main_table.order_id = order_table.entity_id',
                    [
                        'customer_email'
                    ]
                )
                ->group(
                    'main_table.order_id'
                );
        if ($customerId) {
            $orderCollection->addFieldToFilter("main_table.customer_id", (int)$customerId);
        } else {
            $orderCollection->addFieldToFilter("order_table.customer_email", $customerEmail);
        }
        if ($require_order_completed) {
            $orderCollection->getSelect()
                            ->where("order_table.state = ?", self::COMPLATE_STATE);
        }

        if ($orderCollection->getSize()) {
            return true;
        }
        return false;
    }

}
