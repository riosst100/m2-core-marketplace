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

namespace Lof\MarketPermissions\Model\Action\Seller;

/**
 * Class that replaces admin of seller by another one.
 */
class ReplaceSuperUser
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Model\Customer\SellerAttributes
     */
    private $sellerAttributes;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $sellerStructure;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Customer
     */
    private $customerResource;

    /**
     * @var \Lof\MarketPermissions\Api\AclInterface
     */
    private $userRoleManagement;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Model\Customer\SellerAttributes $sellerAttributes
     * @param \Lof\MarketPermissions\Model\Seller\Structure $sellerStructure
     * @param \Lof\MarketPermissions\Model\ResourceModel\Customer $customerResource
     * @param \Lof\MarketPermissions\Api\AclInterface $userRoleManagement
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Model\Customer\SellerAttributes $sellerAttributes,
        \Lof\MarketPermissions\Model\Seller\Structure $sellerStructure,
        \Lof\MarketPermissions\Model\ResourceModel\Customer $customerResource,
        \Lof\MarketPermissions\Api\AclInterface $userRoleManagement
    ) {
        $this->customerRepository = $customerRepository;
        $this->sellerAttributes = $sellerAttributes;
        $this->sellerStructure = $sellerStructure;
        $this->customerResource = $customerResource;
        $this->userRoleManagement = $userRoleManagement;
    }

    /**
     * Convert administrator of the seller to user of the seller, if the administrator of the seller was changed.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int|null $oldSuperUser
     * @param bool $keepActive
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function execute(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $oldSuperUser,
        $keepActive
    ) {
        if ($oldSuperUser && (int)$customer->getId() !== $oldSuperUser) {
            $oldCustomer = $this->customerRepository->getById($oldSuperUser);
            $sellerAttributes = $this->sellerAttributes->getSellerAttributesByCustomer($customer);
            if ($sellerAttributes !== null) {
                $sellerId = $sellerAttributes->getSellerId();
                $sellerAttributes = $oldCustomer->getExtensionAttributes()->getSellerAttributes();
                $sellerAttributes->setCustomerId($oldSuperUser);
                if (!$keepActive) {
                    $sellerAttributes->setStatus(\Lof\MarketPermissions\Api\Data\SellerCustomerInterface::STATUS_INACTIVE);
                }
                $this->customerResource->saveAdvancedCustomAttributes($sellerAttributes);
                $this->userRoleManagement->assignUserDefaultRole($oldSuperUser, $sellerId);
            }
            $this->sellerStructure->moveStructureChildrenToParent($customer->getId());
            $this->sellerStructure->removeCustomerNode($customer->getId());
            $this->sellerStructure->addNode(
                $customer->getId(),
                \Lof\MarketPermissions\Api\Data\StructureInterface::TYPE_CUSTOMER,
                0
            );
            $this->sellerStructure->moveCustomerStructure($oldCustomer->getId(), $customer->getId(), $keepActive);
        }

        if (!$this->sellerStructure->getStructureByCustomerId($customer->getId())) {
            $this->sellerStructure->addNode(
                $customer->getId(),
                \Lof\MarketPermissions\Api\Data\StructureInterface::TYPE_CUSTOMER,
                0
            );
        }
        return $this;
    }
}
