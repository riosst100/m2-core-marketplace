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

use Magento\Authorization\Model\UserContextInterface;

/**
 * Seller role locator.
 */
class RoleLocator implements \Magento\Framework\Authorization\RoleLocatorInterface
{
    /**
     * @var \Magento\Authorization\Model\UserContextInterface
     */
    private $userContext;

    /**
     * @var \Lof\MarketPermissions\Api\AclInterface
     */
    private $roleManagement;

    /**
     * @param \Lof\MarketPermissions\Model\SellerAdminPermission
     */
    private $adminPermission;

    /**
     * @param \Magento\Authorization\Model\UserContextInterface $userContext
     * @param \Lof\MarketPermissions\Api\AclInterface $roleManagement
     * @param \Lof\MarketPermissions\Model\SellerAdminPermission $adminPermission
     */
    public function __construct(
        \Magento\Authorization\Model\UserContextInterface $userContext,
        \Lof\MarketPermissions\Api\AclInterface $roleManagement,
        \Lof\MarketPermissions\Model\SellerAdminPermission $adminPermission
    ) {
        $this->userContext = $userContext;
        $this->roleManagement = $roleManagement;
        $this->adminPermission = $adminPermission;
    }

    /**
     * Retrieve current role.
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        $roleId = null;
        $userId = $this->userContext->getUserId();
        $userType = $this->userContext->getUserType();
        if ($userId && $userType == UserContextInterface::USER_TYPE_CUSTOMER) {
            $roles = $this->roleManagement->getRolesByUserId($userId);
            if (!empty($roles)) {
                $role = array_shift($roles);
                $roleId = $role->getData('role_id');
            } elseif ($this->adminPermission->isGivenUserSellerAdmin($userId)) {
                $roleId = 0;
            }
        }
        return $roleId;
    }
}
