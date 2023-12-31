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
use Lof\MarketPermissions\Model\ResourceModel\Customer;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Lof\MarketPermissions\Model\Seller\Structure;
use Lof\MarketPermissions\Api\SellerManagementInterface;
use Lof\MarketPermissions\Model\Email\Sender;
use Lof\MarketPermissions\Api\AclInterface;

/**
 * Save customer attributes for each seller.
 */
class AttributesSaver
{
    /**
     * @var Customer
     */
    private $customerResource;

    /**
     * @var Structure
     */
    private $sellerStructure;

    /**
     * @var SellerManagementInterface
     */
    private $sellerManagement;

    /**
     * @var Sender
     */
    private $sellerEmailSender;

    /**
     * @var AclInterface
     */
    private $userRoleManagement;

    /**
     * @param Customer $customerResource
     * @param Structure $sellerStructure
     * @param SellerManagementInterface $sellerManagement
     * @param Sender $sellerEmailSender
     * @param AclInterface $userRoleManagement
     */
    public function __construct(
        Customer $customerResource,
        Structure $sellerStructure,
        SellerManagementInterface $sellerManagement,
        Sender $sellerEmailSender,
        AclInterface $userRoleManagement
    ) {
        $this->customerResource = $customerResource;
        $this->sellerStructure = $sellerStructure;
        $this->sellerManagement = $sellerManagement;
        $this->sellerEmailSender = $sellerEmailSender;
        $this->userRoleManagement = $userRoleManagement;
    }

    /**
     * Save customer attributes for seller.
     *
     * @param CustomerInterface $customer
     * @param SellerCustomerInterface $sellerAttributes
     * @param int $sellerId
     * @param bool $isSellerChange
     * @param int $currentCustomerStatus
     * @return void
     * @throws CouldNotSaveException
     */
    public function saveAttributes(
        CustomerInterface $customer,
        SellerCustomerInterface $sellerAttributes,
        $sellerId,
        $isSellerChange,
        $currentCustomerStatus
    ) {
        if ($sellerAttributes && $customer->getId()) {
            $customer->getExtensionAttributes()->setSellerAttributes($sellerAttributes);
            $seller = $this->sellerManagement->getByCustomerId($customer->getId());
            $isSuperUser = $seller ? $seller->getCustomerId() === $customer->getId()
                && $sellerAttributes->getSellerId() === $seller->getSellerId() : false;
            if ($seller && (int)$sellerAttributes->getStatus() === SellerCustomerInterface::STATUS_INACTIVE) {
                if ($isSuperUser) {
                    throw new CouldNotSaveException(
                        __(
                            'The user %1 is the seller admin and cannot be set to inactive. '
                            . 'You must set another user as the seller admin first.',
                            $customer->getFirstname() . ' ' . $customer->getLastname()
                        )
                    );
                }
                $this->sellerStructure->moveStructureChildrenToParent($customer->getId());
            }
            $sellerAttributes->setCustomerId($customer->getId());
            $this->deleteRole($isSuperUser, $customer->getId());
            $this->customerResource->saveAdvancedCustomAttributes($sellerAttributes);
            $this->updateSellerStructure($customer, $sellerId, $isSellerChange);
            $this->sendNotification($customer, $sellerAttributes, $currentCustomerStatus);
        }
    }

    /**
     * Updates seller structure.
     *
     * @param CustomerInterface $customer
     * @param int $sellerId
     * @param bool $isSellerChange
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateSellerStructure(CustomerInterface $customer, $sellerId, $isSellerChange)
    {
        if ($isSellerChange && $sellerId) {
            $this->sellerStructure->moveStructureChildrenToParent($customer->getId());
            $this->sellerStructure->removeCustomerNode($customer->getId());
            $sellerAdmin = $this->sellerManagement->getAdminBySellerId($sellerId);
            $sellerAdminStructure = $this->sellerStructure->getStructureByCustomerId($sellerAdmin->getId());
            $this->sellerStructure->addNode(
                $customer->getId(),
                0,
                $sellerAdminStructure ? $sellerAdminStructure->getId() : 0
            );
        }
    }

    /**
     * Send notification by sender.
     *
     * @param CustomerInterface $customer
     * @param SellerCustomerInterface $sellerAttributes
     * @param int $currentCustomerStatus
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function sendNotification(
        CustomerInterface $customer,
        SellerCustomerInterface $sellerAttributes,
        $currentCustomerStatus
    ) {
        if (isset($currentCustomerStatus) &&
            (int)$currentCustomerStatus !== (int)$sellerAttributes->getStatus()
        ) {
            $this->sellerEmailSender->sendUserStatusChangeNotificationEmail(
                $customer,
                $sellerAttributes->getStatus()
            );
        }
    }

    /**
     * Delete user roles.
     *
     * @param bool $isSuperUser
     * @param int $customerId
     * @return void
     */
    private function deleteRole($isSuperUser, $customerId)
    {
        if ($isSuperUser) {
            $this->userRoleManagement->deleteRoles($customerId);
        }
    }
}
