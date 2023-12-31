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
 * Role data transfer object interface.
 */
interface RoleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ROLE_ID = 'role_id';
    const SORT_ORDER = 'sort_order';
    const ROLE_NAME = 'role_name';
    const SELLER_ID = 'seller_id';
    /**#@-*/

    /**
     * Set id.
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function setId($id);

    /**
     * Set role name.
     *
     * @param string $name
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function setRoleName($name);

    /**
     * Set seller id.
     *
     * @param int $id
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function setSellerId($id);

    /**
     * Set permissions.
     *
     * @param \Lof\MarketPermissions\Api\Data\PermissionInterface[] $permissions
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function setPermissions(array $permissions);

    /**
     * Set extension attributes.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleExtensionInterface $extensionAttribute
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    public function setExtensionAttributes(\Lof\MarketPermissions\Api\Data\RoleExtensionInterface $extensionAttribute);

    /**
     * Get role id.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get role name.
     *
     * @return string|null
     */
    public function getRoleName();

    /**
     * Get permissions.
     *
     * @return \Lof\MarketPermissions\Api\Data\PermissionInterface[]
     */
    public function getPermissions();

    /**
     * Get seller id.
     *
     * @return int|null
     */
    public function getSellerId();

    /**
     * Get extension attributes.
     *
     * @return \Lof\MarketPermissions\Api\Data\RoleExtensionInterface|null
     */
    public function getExtensionAttributes();
}
