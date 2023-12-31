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

namespace Lof\MarketPermissions\Plugin\Customer\Api\CustomerRepository;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Lof\MarketPermissions\Model\Customer\SellerAttributes;
use Lof\MarketPermissions\Api\SellerRepositoryInterface;

/**
 * A plugin for customer save operation for processing seller routines.
 */
class Save
{
    /**
     * @var \Lof\MarketPermissions\Model\Customer\SellerAttributes
     */
    private $customerSaveAttributes;

    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * @param SellerAttributes $customerSaveAttributes
     * @param SellerRepositoryInterface $sellerRepository
     */
    public function __construct(
        SellerAttributes $customerSaveAttributes,
        SellerRepositoryInterface $sellerRepository
    ) {
        $this->customerSaveAttributes = $customerSaveAttributes;
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Before customer save.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerInterface $customer
     * @param null $passwordHash [optional]
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSave(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        $this->customerSaveAttributes->updateSellerAttributes($customer);
        $customer = $this->setCustomerGroup($customer);
        return [$customer, $passwordHash];
    }

    /**
     * Set customer group.
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setCustomerGroup(CustomerInterface $customer)
    {
        $sellerId = $this->customerSaveAttributes->getSellerId();
        if ($sellerId) {
            $seller = $this->sellerRepository->get($sellerId);
            $customer->setGroupId($seller->getGroupId());
        }
        return $customer;
    }

    /**
     * After customer save.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return CustomerInterface
     * @throws CouldNotSaveException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer
    ) {
        $this->customerSaveAttributes->saveSellerAttributes($customer);
        return $customer;
    }
}
