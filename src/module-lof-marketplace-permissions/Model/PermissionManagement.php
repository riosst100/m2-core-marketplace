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

use Magento\Framework\Model\AbstractModel;

/**
 * Permission management: operations with role permissions.
 */
class PermissionManagement extends AbstractModel implements PermissionManagementInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\ResourcePool
     */
    private $resourcePool;

    /**
     * @var \Lof\MarketPermissions\Api\Data\PermissionInterfaceFactory
     */
    private $permissionFactory;

    /**
     * @var \Magento\Framework\Acl\AclResource\ProviderInterface
     */
    private $resourceProvider;

    /**
     * PermissionManagement constructor.
     * @param \Lof\MarketPermissions\Model\ResourcePool $resourcePool
     * @param \Lof\MarketPermissions\Api\Data\PermissionInterfaceFactory $permissionFactory
     * @param \Magento\Framework\Acl\AclResource\ProviderInterface $resourceProvider
     */
    public function __construct(
        \Lof\MarketPermissions\Model\ResourcePool $resourcePool,
        \Lof\MarketPermissions\Api\Data\PermissionInterfaceFactory $permissionFactory,
        \Magento\Framework\Acl\AclResource\ProviderInterface $resourceProvider
    ) {
        $this->resourcePool = $resourcePool;
        $this->permissionFactory = $permissionFactory;
        $this->resourceProvider = $resourceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveAllowedResources(array $permissions)
    {
        $allowedResources = [];
        foreach ($permissions as $permission) {
            if ($permission->getPermission() == \Lof\MarketPermissions\Api\Data\PermissionInterface::ALLOW_PERMISSION) {
                $allowedResources[] = $permission->getResourceId();
            }
        }
        return $allowedResources;
    }

    /**
     * {@inheritdoc}
     */
    public function retrieveDefaultPermissions()
    {
        $resources = $this->resourcePool->getDefaultResources();
        return $this->populatePermissions($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function populatePermissions(array $allowedResources)
    {
        if ($allowedResources) {
            $allowedResources[] = \Lof\MarketPermissions\Controller\Marketplace\AbstractAction::SELLER_RESOURCE;
        }
        $resources = $this->getFlatResources($this->resourceProvider->getAclResources());
        $permissions = $this->preparePermissions($resources, $allowedResources);
        return $permissions;
    }

    /**
     * Preparation of permissions and verify the permissions for the structure of the resource tree.
     *
     * @param array $resources
     * @param array $allowedResources
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    private function preparePermissions(array $resources, array $allowedResources)
    {
        $permissions = [];
        foreach ($resources as $resource => $data) {
            $permission = $this->permissionFactory->create();
            if (in_array($resource, $allowedResources)) {
                if (array_diff($data['requiredPermissions'], $allowedResources)) {
                    throw new \Magento\Framework\Exception\InputException(
                        __('Unable to set "allow" for the resource because its parent resource(s) is set to "deny".')
                    );
                }
                $permissions[] = $permission
                    ->setPermission(\Lof\MarketPermissions\Api\Data\PermissionInterface::ALLOW_PERMISSION)
                    ->setResourceId($resource);
            } else {
                $permissions[] = $permission
                    ->setPermission(\Lof\MarketPermissions\Api\Data\PermissionInterface::DENY_PERMISSION)
                    ->setResourceId($resource);
            }
            if (!empty($data['children'])) {
                $permissions = array_merge(
                    $permissions,
                    $this->preparePermissions($data['children'], $allowedResources)
                );
            }
        }
        return $permissions;
    }

    /**
     * Get flat resources.
     *
     * @param array $resources
     * @param array $parents [optional]
     * @return array
     */
    private function getFlatResources(array $resources, array $parents = [])
    {
        $result = [];
        foreach ($resources as $resource) {
            $parents[$resource['id']] = $resource['id'];
            $result[$resource['id']]['requiredPermissions'] = $parents;
            if (!empty($resource['children'])) {
                $result[$resource['id']]['children'] = $this->getFlatResources($resource['children'], $parents);
            }
            array_pop($parents);
        }
        return $result;
    }
}
