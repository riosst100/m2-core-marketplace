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

use Magento\Framework\Exception\NoSuchEntityException;

class SellerAdminPermission
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
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $customerContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Api\SellerRepositoryInterface $sellerRepository
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $customerContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Api\SellerRepositoryInterface $sellerRepository
    ) {
        $this->sellerRepository = $sellerRepository;
        $this->customerContext = $customerContext;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check current user is seller admin.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isCurrentUserSellerAdmin()
    {
        $customer = $this->customerRepository->getById($this->customerContext->getUserId());
        return $this->isUserSellerAdmin($customer);
    }

    /**
     * Check if given user is a seller admin.
     *
     * @param int $userId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isGivenUserSellerAdmin($userId)
    {
        $customer = $this->customerRepository->getById($userId);
        return $this->isUserSellerAdmin($customer);
    }

    /**
     * Check if a user is a seller admin.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return bool
     */
    private function isUserSellerAdmin(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $isSellerAdmin = false;

        if ($customer->getExtensionAttributes() !== null
            && $customer->getExtensionAttributes()->getSellerAttributes() !== null
        ) {
            try {
                $seller = $this->sellerRepository->get(
                    $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId()
                );

                $isSellerAdmin = $customer->getId() == $seller->getCustomerId();
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return $isSellerAdmin;
    }
}
