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

namespace Lof\MarketPermissions\Model;

/**
 * Management class for role entity.
 */
class RoleManagement implements \Lof\MarketPermissions\Api\RoleManagementInterface
{
    /**
     * @var int
     */
    private $sellerAdminRoleId = 0;

    /**
     * @var string
     */
    private $sellerAdminRoleName = 'Seller Administrator';

    /**
     * @var int
     */
    private $sellerManagerRoleId = PHP_INT_MAX;

    /**
     * @var string
     */
    private $sellerManagerRoleName = 'Purchaser\'s Manager';

    /**
     * @var string
     */
    private $sellerDefaultRoleName = 'Default User';

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory
     */
    private $roleCollectionFactory;

    /**
     * @var \Lof\MarketPermissions\Model\RoleFactory
     */
    private $roleFactory;

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    private $sellerAdminRole;

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleInterface
     */
    private $sellerManagerRole;

    /**
     * @param \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
     * @param \Lof\MarketPermissions\Model\RoleFactory $roleFactory
     */
    public function __construct(
        \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory,
        \Lof\MarketPermissions\Model\RoleFactory $roleFactory
    ) {
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->roleFactory = $roleFactory;
    }

    /**
     * Get seller admin role ID.
     *
     * @return int
     */
    public function getSellerAdminRoleId()
    {
        return $this->sellerAdminRoleId;
    }

    /**
     * Get seller admin role ID.
     *
     * @return int
     */
    public function getSellerManagerRoleId()
    {
        return $this->sellerManagerRoleId;
    }

    /**
     * Get seller admin role name.
     *
     * @return string
     */
    public function getSellerAdminRoleName()
    {
        return $this->sellerAdminRoleName;
    }

    /**
     * Get seller manager role name.
     *
     * @return string
     */
    public function getSellerManagerRoleName()
    {
        return $this->sellerManagerRoleName;
    }

    /**
     * Get seller default role name.
     *
     * @return string
     */
    public function getSellerDefaultRoleName()
    {
        return $this->sellerDefaultRoleName;
    }

    /**
     * @inheritdoc
     */
    public function getRolesBySellerId($sellerId, $includeAdminRole = true)
    {
        $roleCollection = $this->roleCollectionFactory->create();
        $roleCollection->addFieldToFilter('seller_id', ['eq' => $sellerId])
            ->setOrder('role_id', 'ASC')
            ->load();
        $roles = $roleCollection->getItems();
        if ($includeAdminRole === true) {
            $roles[] = $this->getAdminRole();
        }

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getSellerDefaultRole($sellerId)
    {
        $roles = $this->getRolesBySellerId($sellerId, false);

        return reset($roles);
    }

    /**
     * @inheritdoc
     */
    public function getAdminRole()
    {
        if ($this->sellerAdminRole === null) {
            $this->sellerAdminRole = $this->roleFactory->create();
            $this->sellerAdminRole->setId($this->getSellerAdminRoleId());
            $roleName = __($this->getSellerAdminRoleName());
            $this->sellerAdminRole->setRoleName($roleName);
        }

        return $this->sellerAdminRole;
    }

    /**
     * @inheritdoc
     */
    public function getManagerRole()
    {
        if ($this->sellerManagerRole === null) {
            $this->sellerManagerRole = $this->roleFactory->create();
            $this->sellerManagerRole->setId($this->getSellerManagerRoleId());
            $roleName = __($this->getSellerManagerRoleName());
            $this->sellerManagerRole->setRoleName($roleName);
        }

        return $this->sellerManagerRole;
    }
}
