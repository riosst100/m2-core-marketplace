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
 * Interface for managing seller roles.
 */
interface RoleManagementInterface
{
    /**
     * Get roles by seller id.
     *
     * @param int $sellerId
     * @param bool $includeAdminRole [optional]
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface[]
     */
    public function getRolesBySellerId($sellerId, $includeAdminRole = true);

    /**
     * Get admin role.
     *
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function getAdminRole();

    /**
     * Get manager role.
     *
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     * @since 100.2.0
     */
    public function getManagerRole();

    /**
     * Get seller default role.
     *
     * @param int $sellerId
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function getSellerDefaultRole($sellerId);
}
