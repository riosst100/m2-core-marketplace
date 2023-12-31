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

namespace Lof\MarketPermissions\Model\SaveHandler;

use Lof\MarketPermissions\Model\SaveHandlerInterface;

/**
 * Default role creator.
 */
class DefaultRole implements SaveHandlerInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\RoleFactory
     */
    private $roleFactory;

    /**
     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var \Lof\MarketPermissions\Model\PermissionManagementInterface
     */
    private $permissionManagement;

    /**
     * @var \Lof\MarketPermissions\Model\RoleManagement
     */
    private $roleManagement;

    /**
     * @param \Lof\MarketPermissions\Model\RoleFactory $roleFactory
     * @param \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository
     * @param \Lof\MarketPermissions\Model\PermissionManagementInterface $permissionManagement
     * @param \Lof\MarketPermissions\Model\RoleManagement $roleManagement
     */
    public function __construct(
        \Lof\MarketPermissions\Model\RoleFactory $roleFactory,
        \Lof\MarketPermissions\Api\RoleRepositoryInterface $roleRepository,
        \Lof\MarketPermissions\Model\PermissionManagementInterface $permissionManagement,
        \Lof\MarketPermissions\Model\RoleManagement $roleManagement
    ) {
        $this->roleFactory = $roleFactory;
        $this->roleRepository = $roleRepository;
        $this->permissionManagement = $permissionManagement;
        $this->roleManagement = $roleManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($seller, $initialSeller)
    {
        if (!$initialSeller->getId()) {
            $role = $this->roleFactory->create();
            $role->setRoleName($this->roleManagement->getSellerDefaultRoleName());
            $role->setSellerId($seller->getId());
            $role->setPermissions($this->permissionManagement->retrieveDefaultPermissions());
            $this->roleRepository->save($role);
        }
    }
}
