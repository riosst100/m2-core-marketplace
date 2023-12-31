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

use Lof\MarketPermissions\Model\ResourceModel\Permission\CollectionFactory as PermissionCollectionFactory;

/**
 * Class for managing role permissions.
 */
class Permission
{
    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Permission\CollectionFactory
     */
    private $permissionCollectionFactory;

    /**
     * @var \Magento\Framework\Acl\Data\CacheInterface
     */
    private $aclDataCache;

    /**
     * @var \Lof\MarketPermissions\Api\AclInterface
     */
    private $userRoleManagement;

    /**
     * @param PermissionCollectionFactory $permissionCollectionFactory
     * @param \Magento\Framework\Acl\Data\CacheInterface $aclDataCache
     * @param \Lof\MarketPermissions\Api\AclInterface $userRoleManagement
     */
    public function __construct(
        PermissionCollectionFactory $permissionCollectionFactory,
        \Magento\Framework\Acl\Data\CacheInterface $aclDataCache,
        \Lof\MarketPermissions\Api\AclInterface $userRoleManagement
    ) {
        $this->permissionCollectionFactory = $permissionCollectionFactory;
        $this->aclDataCache = $aclDataCache;
        $this->userRoleManagement = $userRoleManagement;
    }

    /**
     * Gets a number of users assigned to the role.
     *
     * @param int $roleId
     * @return int
     */
    public function getRoleUsersCount($roleId)
    {
        return count($this->userRoleManagement->getUsersByRoleId($roleId));
    }

    /**
     * Get role permissions.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface  $role
     * @return array
     */
    public function getRolePermissions(\Lof\MarketPermissions\Api\Data\RoleInterface  $role)
    {
        $permissionCollection = $this->permissionCollectionFactory->create();
        $permissionCollection->addFieldToFilter('role_id', ['eq' => $role->getId()])->load();
        return $permissionCollection->getItems();
    }

    /**
     * Delete role permissions.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface  $role
     * @return void
     */
    public function deleteRolePermissions(\Lof\MarketPermissions\Api\Data\RoleInterface  $role)
    {
        $permissions = $this->getRolePermissions($role);
        foreach ($permissions as $permission) {
            $permission->delete();
        }
        $this->aclDataCache->clean();
    }

    /**
     * Save role permissions.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface  $role
     * @return void
     */
    public function saveRolePermissions(\Lof\MarketPermissions\Api\Data\RoleInterface  $role)
    {
        $permissions = $role->getPermissions();
        $this->deleteRolePermissions($role);
        foreach ($permissions as $permission) {
            $permission->setRoleId($role->getId());
            $permission->save();
        }
        $this->aclDataCache->clean();
    }
}
