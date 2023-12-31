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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Lof\MarketPermissions\Api\Data\SellerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;

/**
 * Class Permission
 */
class Permission implements PermissionInterface
{
//    /**
//     * Seller locked statuses array
//     */
//    const SELLER_LOCKED_STATUSES = [
//        SellerInterface::STATUS_REJECTED,
//        SellerInterface::STATUS_PENDING
//    ];

    /**
     * @var \Lof\MarketPermissions\Api\SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Api\AuthorizationInterface
     */
    private $authorization;

    /**
     * @param \Lof\MarketPermissions\Api\SellerManagementInterface $sellerManagement
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Api\AuthorizationInterface $authorization
     */
    public function __construct(
        \Lof\MarketPermissions\Api\SellerManagementInterface $sellerManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Api\AuthorizationInterface $authorization
    ) {
        $this->sellerManagement = $sellerManagement;
        $this->customerRepository = $customerRepository;
        $this->authorization = $authorization;
    }

    /**
     * {@inheritdoc}
     */
    public function isCheckoutAllowed(
        CustomerInterface $customer,
        $isNegotiableQuoteActive = false
    ) {
        $seller = $this->sellerManagement->getByCustomerId($customer->getId());

        if (!$seller) {
            return true;
        }

        return !$this->isSellerBlocked($customer) && $this->hasPermission($isNegotiableQuoteActive);
    }

    /**
     * {@inheritdoc}
     */
    public function isLoginAllowed(CustomerInterface $customer)
    {
        return !$this->isSellerLocked($customer) && !$this->isCustomerLocked($customer);
    }

    /**
     * Is customer seller locked.
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    private function isSellerLocked(CustomerInterface $customer)
    {
        $seller = $this->sellerManagement->getByCustomerId($customer->getId());
        if ($seller) {
            return $seller->getStatus() == SellerCustomerInterface::STATUS_INACTIVE;
        }
        return false;
    }

    /**
     * Is customer locked.
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    private function isCustomerLocked(CustomerInterface $customer)
    {
        $isCustomerLocked = false;
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getSellerAttributes()
            && $customer
                ->getExtensionAttributes()
                ->getSellerAttributes()
                ->getStatus() == SellerCustomerInterface::STATUS_INACTIVE
        ) {
            $isCustomerLocked = true;
        }
        return $isCustomerLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isSellerBlocked(CustomerInterface $customer)
    {
        $seller = $this->sellerManagement->getByCustomerId($customer->getId());
        return $seller && $seller->getStatus() == SellerCustomerInterface::STATUS_ACTIVE;
    }

    /**
     * Check if has permission fot method.
     *
     * @param bool $isNegotiableQuoteActive
     * @return bool
     */
    private function hasPermission($isNegotiableQuoteActive = false)
    {
        if ($isNegotiableQuoteActive) {
            return $this->authorization->isAllowed('Magento_NegotiableQuote::checkout');
        } else {
            return $this->authorization->isAllowed('Magento_Sales::place_order');
        }
    }
}
