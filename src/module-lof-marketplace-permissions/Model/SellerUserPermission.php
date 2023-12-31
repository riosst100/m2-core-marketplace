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

namespace Lof\MarketPermissions\Model;

/**
 * Seller user permission class.
 */
class SellerUserPermission
{
    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $customerContext;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Api\StatusServiceInterface
     */
    private $moduleConfig;

    /**
     * SellerAdminPermission constructor.
     *
     * @param \Magento\Authorization\Model\UserContextInterface $customerContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $customerContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig
    ) {
        $this->customerContext = $customerContext;
        $this->customerRepository = $customerRepository;
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * Check is current user seller user.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isCurrentUserSellerUser()
    {
        $customer = $this->customerRepository->getById($this->customerContext->getUserId());
        return $this->isUserSellerUser($customer);
    }

    /**
     * Check is user a seller user.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    private function isUserSellerUser(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        return $this->moduleConfig->isActive() &&
            $customer->getExtensionAttributes() !== null &&
            $customer->getExtensionAttributes()->getSellerAttributes() !== null &&
            $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId();
    }
}
