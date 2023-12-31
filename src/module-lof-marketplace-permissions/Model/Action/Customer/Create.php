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

namespace Lof\MarketPermissions\Model\Action\Customer;

/**
 * Class that creates a customer and assigns it to seller
 */
class Create
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    private $customerManager;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AccountManagementInterface  $customerManager
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AccountManagementInterface $customerManager,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerManager = $customerManager;
        $this->structureManager = $structureManager;
    }

    /**
     * Create a customer and assigns it to the seller
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int $targetId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute(\Magento\Customer\Api\Data\CustomerInterface $customer, $targetId)
    {
        if ($customer->getId()) {
            $this->customerRepository->save($customer);
        } else {
            $this->customerManager->createAccount($customer);
        }
        /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customer */
        $customer = $this->customerRepository->get($customer->getEmail());
        $this->addCustomerToStructure($customer, $targetId);

        return $customer;
    }

    /**
     * Add customer to structure.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int $targetId
     * @return void
     */
    private function addCustomerToStructure(\Magento\Customer\Api\Data\CustomerInterface $customer, $targetId)
    {
        $structure = $this->structureManager->getStructureByCustomerId($customer->getId());
        if ($structure && $targetId && $structure->getId()) {
            $this->structureManager->removeCustomerNode($customer->getId());
            $this->structureManager->addNode(
                $customer->getId(),
                \Lof\MarketPermissions\Api\Data\StructureInterface::TYPE_CUSTOMER,
                $targetId
            );
        }
    }
}
