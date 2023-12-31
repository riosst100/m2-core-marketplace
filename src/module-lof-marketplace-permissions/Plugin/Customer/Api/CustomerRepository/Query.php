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
use Magento\Framework\Api\DataObjectHelper;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory as SellerCustomerExtension;
use Lof\MarketPermissions\Model\Customer\SellerAttributes;

/**
 * A plugin for customer get operations for processing seller routines.
 */
class Query
{
    /**
     * @var \Magento\Framework\Api\ExtensionAttributesFactory
     */
    private $extensionFactory;

    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory
     */
    private $sellerCustomerAttributes;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var \Lof\MarketPermissions\Model\Customer\SellerAttributes
     */
    private $customerSaveAttributes;

    /**
     * CustomerRepository constructor.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param SellerCustomerExtension $sellerCustomerAttributes
     * @param DataObjectHelper $dataObjectHelper
     * @param SellerAttributes $customerSaveAttributes
     */
    public function __construct(
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        SellerCustomerExtension $sellerCustomerAttributes,
        DataObjectHelper $dataObjectHelper,
        SellerAttributes $customerSaveAttributes
    ) {
        $this->extensionFactory = $extensionFactory;
        $this->sellerCustomerAttributes = $sellerCustomerAttributes;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->customerSaveAttributes = $customerSaveAttributes;
    }

    /**
     * After get customer.
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(CustomerRepositoryInterface $subject, CustomerInterface $customer)
    {
        return $this->getCustomer($customer);
    }

    /**
     * After get customer by ID.
     *
     * @param CustomerRepositoryInterface $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetById(CustomerRepositoryInterface $subject, CustomerInterface $customer)
    {
        return $this->getCustomer($customer);
    }

    /**
     * Get customer.
     *
     * @param CustomerInterface $customer
     * @return CustomerInterface
     */
    private function getCustomer(CustomerInterface $customer)
    {
        if ($customer->getExtensionAttributes()
            && $customer->getExtensionAttributes()->getSellerAttributes()
        ) {
            return $customer;
        }

        if (!$customer->getExtensionAttributes()) {
            $customerExtension = $this->extensionFactory->create(CustomerInterface::class);
            $customer->setExtensionAttributes($customerExtension);
        }

        $sellerAttributes = $this->getSellerAttributes($customer);

        if ($sellerAttributes) {
            $customer->getExtensionAttributes()->setSellerAttributes($sellerAttributes);
        }

        return $customer;
    }

    /**
     * Get seller attributes.
     *
     * @param CustomerInterface $customer
     * @return \Lof\MarketPermissions\Api\Data\SellerCustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getSellerAttributes(CustomerInterface $customer)
    {
        try {
            $sellerAttributesArray = $this->customerSaveAttributes->getSellerAttributes($customer);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong')
            );
        }
        if (!$sellerAttributesArray) {
            return null;
        }
        $sellerAttributes = $this->sellerCustomerAttributes->create();
        $this->dataObjectHelper->populateWithArray(
            $sellerAttributes,
            $sellerAttributesArray,
            \Lof\MarketPermissions\Api\Data\SellerCustomerInterface::class
        );
        return $sellerAttributes;
    }
}
