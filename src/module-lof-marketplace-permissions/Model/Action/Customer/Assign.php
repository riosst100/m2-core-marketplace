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

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class for assigning a role to customer.
 */
class Assign
{
    /**
     * @var \Lof\MarketPermissions\Api\AclInterface
     */
    private $acl;

    /**
     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param \Lof\MarketPermissions\Api\AclInterface $acl
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        \Lof\MarketPermissions\Api\AclInterface $acl,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
    ) {
        $this->acl = $acl;
        $this->roleRepository = $roleRepository;
    }

    /**
     * Assign role to customer.
     *
     * @param CustomerInterface $customer
     * @param int $roleId
     * @return CustomerInterface
     */
    public function assignCustomerRole(CustomerInterface $customer, $roleId)
    {
        $role = $this->roleRepository->get($roleId);
        $sellerId = $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId();

        if ($role && $role->getSellerId() == $sellerId) {
            $this->acl->assignRoles($customer->getId(), [$role]);
        }

        return $customer;
    }
}
