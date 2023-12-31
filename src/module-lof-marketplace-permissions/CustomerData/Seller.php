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

namespace Lof\MarketPermissions\CustomerData;

use Magento\Framework\Exception\NoSuchEntityException;
use Lof\MarketPermissions\Api\SellerManagementInterface;

/**
 * Seller section
 */
class Seller implements \Magento\Customer\CustomerData\SectionSourceInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    protected $sellerContext;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Model\Customer\PermissionInterface
     */
    protected $permission;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * @var SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Model\Customer\PermissionInterface $permission
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param SellerManagementInterface $sellerManagement
     */
    public function __construct(
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Model\Customer\PermissionInterface $permission,
        \Magento\Framework\App\Http\Context $httpContext,
        SellerManagementInterface $sellerManagement
    ) {
        $this->sellerContext = $sellerContext;
        $this->customerRepository = $customerRepository;
        $this->permission = $permission;
        $this->httpContext = $httpContext;
        $this->sellerManagement = $sellerManagement;
    }

    /**
     * @inheritdoc
     */
    public function getSectionData()
    {
        $customer = $this->getCustomer();
        if ($customer === null) {
            return [];
        }
        return [
            'is_checkout_allowed' => $this->permission->isCheckoutAllowed($customer),
//            'is_seller_blocked' => $this->permission->isSellerBlocked($customer),
//            'is_login_allowed' => $this->permission->isLoginAllowed($customer),
            'is_enabled' => (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH),
            'has_customer_seller' => $this->hasCustomerSeller(),
            'is_storefront_registration_allowed' => $this->sellerContext->isStorefrontRegistrationAllowed(),
            'is_seller_admin' => $this->isSellerAdmin(),
        ];
    }

    /**
     * Get current customer.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomer()
    {
        try {
            $customer = $this->customerRepository->getById($this->sellerContext->getCustomerId());
        } catch (NoSuchEntityException $e) {
            $customer = null;
        }

        return $customer;
    }

    /**
     * Has current customer seller.
     *
     * @return bool
     */
    private function hasCustomerSeller()
    {
        $hasSeller = false;
        $customerId = $this->sellerContext->getCustomerId();
        if ($customerId) {
            $seller = $this->sellerManagement->getByCustomerId($customerId);
            if ($seller) {
                $hasSeller = true;
            }
        }

        return $hasSeller;
    }

    /**
     * Check if the current customer is seller admin
     *
     * @return bool
     */
    private function isSellerAdmin(): bool
    {
        $customerId = $this->sellerContext->getCustomerId();
        if ($customerId) {
            $seller = $this->sellerManagement->getByCustomerId($customerId);
            if ($seller) {
                $sellerId = $seller->getSellerId();
                $sellerAdminId = $this->sellerManagement->getAdminBySellerId($sellerId)->getId();

                return $customerId === $sellerAdminId;
            }
        }
        return false;
    }
}
