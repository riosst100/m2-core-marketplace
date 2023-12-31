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

namespace Lof\MarketPermissions\Model\Authorization;

class PermissionProvider
{
    /**
     * \Lof\MarketPermissions\Model\ResourceModel\Permission\Collection
     */
    private $permissionCollection;

    /**
     * @var \Lof\MarketPermissions\Model\ResourcePool
     */
    private $resourcePool;

    /**
     * PermissionProvider constructor.
     *
     * @param \Lof\MarketPermissions\Model\ResourceModel\Permission\Collection $permissionCollection
     * @param \Lof\MarketPermissions\Model\ResourcePool $resourcePool
     */
    public function __construct(
        \Lof\MarketPermissions\Model\ResourceModel\Permission\Collection $permissionCollection,
        \Lof\MarketPermissions\Model\ResourcePool $resourcePool
    ) {
        $this->permissionCollection = $permissionCollection;
        $this->resourcePool = $resourcePool;
    }

    /**
     * Retrieve permissions hash array.
     *
     * @param int $roleId
     * @return array
     */
    public function retrieveRolePermissions($roleId)
    {
        return $this->permissionCollection
            ->addFieldToFilter('role_id', ['eq' => $roleId])
            ->toOptionHash('resource_id', 'permission');
    }

    /**
     * Retrieve default role permissions.
     *
     * @return array
     */
    public function retrieveDefaultPermissions()
    {
        $permissions = [];
        $resources = $this->resourcePool->getDefaultResources();
        foreach ($resources as $resource) {
            $permissions[$resource] = 'allow';
        }

        return $permissions;
    }
}
