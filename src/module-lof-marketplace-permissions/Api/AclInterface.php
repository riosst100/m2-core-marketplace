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

namespace Lof\MarketPermissions\Api;

/**
 * Access control list interface.
 */
interface AclInterface
{
    /**
     * Change a role for a seller user.
     *
     * @param int $userId
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface[] $roles
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return bool
     */
    public function assignRoles($userId, array $roles);

    /**
     * Get the list of roles by user id.
     *
     * @param int $userId
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface[]
     */
    public function getRolesByUserId($userId);

    /**
     * View the list of seller users assigned to a specified role.
     *
     * @param int $roleId
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
     */
    public function getUsersByRoleId($roleId);

    /**
     * Get users count by role id.
     *
     * @param int $roleId
     * @return int
     */
    public function getUsersCountByRoleId($roleId);

    /**
     * Assign default seller role for a user.
     *
     * @param int $userId
     * @param int $sellerId
     * @return void
     */
    public function assignUserDefaultRole($userId, $sellerId);

    /**
     * Delete role for a user.
     *
     * @param int $userId
     * @return void
     */
    public function deleteRoles($userId);
}
