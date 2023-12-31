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

namespace Lof\MarketPermissions\Model\Role;

/**
 * Validator for Role data.
 */
class Validator
{
    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Lof\MarketPermissions\Api\AclInterface
     */
    private $userRoleManagement;

    /**
     * @var \Lof\MarketPermissions\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     * @param \Lof\MarketPermissions\Api\AclInterface $userRoleManagement
     * @param \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        \Lof\MarketPermissions\Api\SellerRepositoryInterface $sellerRepository,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository,
        \Lof\MarketPermissions\Api\AclInterface $userRoleManagement,
        \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
    ) {
        $this->sellerRepository = $sellerRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleManagement = $userRoleManagement;
        $this->roleManagement = $roleManagement;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Merges requested role object onto the original role and validate role data.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface $requestedRole
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function retrieveRole(\Lof\MarketPermissions\Api\Data\RoleInterface $requestedRole)
    {
        if ($requestedRole->getId()) {
            $requestedSellerId = $requestedRole->getSellerId();
            $originalRole = $this->roleRepository->get($requestedRole->getId());
            $this->dataObjectHelper->mergeDataObjects(
                \Lof\MarketPermissions\Api\Data\RoleInterface::class,
                $originalRole,
                $requestedRole
            );
            $role = $originalRole;
            if ($requestedSellerId && $role->getSellerId() != $requestedSellerId) {
                throw new \Magento\Framework\Exception\InputException(
                    __(
                        'Invalid value of "%value" provided for the %fieldName field.',
                        ['fieldName' => 'seller_id', 'value' => $requestedSellerId]
                    )
                );
            }
        } else {
            $role = $requestedRole;
        }
        if (!$role->getRoleName()) {
            throw new \Magento\Framework\Exception\InputException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'role_name'])
            );
        }
        if (!$role->getId() && !$role->getSellerId()) {
            throw new \Magento\Framework\Exception\InputException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'seller_id'])
            );
        }
        try {
            $this->sellerRepository->get($role->getSellerId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue',
                    ['fieldName' => 'seller_id', 'fieldValue' => $role->getSellerId()]
                )
            );
        }

        return $role;
    }

    /**
     * Validate permissions before saving the role.
     *
     * @param \Lof\MarketPermissions\Api\Data\PermissionInterface[] $permissions
     * @param array $allowedResources
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    public function validatePermissions(array $permissions, array $allowedResources)
    {
        $allResources = [];
        foreach ($permissions as $permission) {
            $allResources[] = $permission->getResourceId();
        }
        $invalidResources = array_diff($allowedResources, $allResources);
        if ($invalidResources) {
            throw new \Magento\Framework\Exception\InputException(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['fieldName' => 'resource_id', 'value' => $invalidResources[0]]
                )
            );
        }
    }

    /**
     * Validates the role before delete.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface $role
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function validateRoleBeforeDelete(\Lof\MarketPermissions\Api\Data\RoleInterface $role)
    {
        $roleUsers = $this->userRoleManagement->getUsersCountByRoleId($role->getId());
        if ($roleUsers) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __(
                    'This role cannot be deleted because users are assigned to it. '
                    . 'Reassign the users to another role to continue.'
                )
            );
        }
        $roles = $this->roleManagement->getRolesBySellerId($role->getSellerId(), false);
        if (count($roles) <= 1) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __(
                    'You cannot delete a role when it is the only role in the seller. '
                    . 'You must create another role before deleting this role.'
                )
            );
        }
    }

    /**
     * Check if Role exist.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface $role
     * @param int $roleId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function checkRoleExist(
        \Lof\MarketPermissions\Api\Data\RoleInterface $role,
        $roleId
    ) {
        if (!$role->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('No such entity with %fieldName = %fieldValue', ['fieldName' => 'roleId', 'fieldValue' => $roleId])
            );
        }
    }
}
