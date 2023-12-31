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

namespace Lof\MarketPermissions\Api\Data;

/**
 * Permission interface.
 */
interface PermissionInterface
{
    /**
     * Permission id.
     */
    const PERMISSION_ID = 'permission_id';

    /**
     * Role id.
     */
    const ROLE_ID = 'role_id';

    /**
     * Resource id.
     */
    const RESOURCE_ID = 'resource_id';

    /**
     * Permission for the resource.
     */
    const PERMISSION = 'permission';

    /**
     * The value for allowed permission.
     */
    const ALLOW_PERMISSION = 'allow';

    /**
     * The value for denied permission.
     */
    const DENY_PERMISSION = 'deny';

    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id.
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\PermissionInterface
     */
    public function setId($id);

    /**
     * Get role id.
     *
     * @return int|null
     */
    public function getRoleId();

    /**
     * Set role id.
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\PermissionInterface
     */
    public function setRoleId($id);

    /**
     * Get resource id.
     *
     * @return string
     */
    public function getResourceId();

    /**
     * Set resource id.
     *
     * @param string $id
     * @return \Lof\MarketPermissions\Api\Data\PermissionInterface
     */
    public function setResourceId($id);

    /**
     * Get permission.
     *
     * Function will return permission values assigned in \Magento\CatalogPermissions\Model\Permission
     *    value: -2 - for deny permission
     *    value: -1 - for allow permission
     *    value: 0 - for apply parent permission
     *
     * @return string
     */
    public function getPermission();

    /**
     * Set permission.
     *
     * Function will set permission values assigned in \Magento\CatalogPermissions\Model\Permission
     *    value: -2 - for deny permission
     *    value: -1 - for allow permission
     *    value: 0 - for apply parent permission
     *
     * @param string $permission
     *
     * @return \Lof\MarketPermissions\Api\Data\PermissionInterface
     */
    public function setPermission($permission);
}
