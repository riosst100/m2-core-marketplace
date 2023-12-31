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

namespace Lof\MarketPermissions\Model\Authorization\Loader;

use Lof\MarketPermissions\Model\Permission;
use \Magento\Framework\Acl;

/**
 * Populate ACL permissions for all seller roles.
 */
class Rule implements \Magento\Framework\Acl\LoaderInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Permission\Collection
     */
    private $collection;

    /**
     * @var \Magento\Framework\Acl\RootResource
     */
    private $rootResource;

    /**
     * @var \Magento\Framework\Acl\AclResource\ProviderInterface
     */
    private $resourceProvider;

    /**
     * @var \Lof\MarketPermissions\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Lof\MarketPermissions\Model\SellerUser
     */
    private $sellerUser;

    /**
     * @param \Magento\Framework\Acl\RootResource $rootResource
     * @param \Lof\MarketPermissions\Model\ResourceModel\Permission\Collection $collection
     * @param \Magento\Framework\Acl\AclResource\ProviderInterface $resourceProvider
     * @param \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement
     * @param \Lof\MarketPermissions\Model\SellerUser $sellerUser
     */
    public function __construct(
        \Magento\Framework\Acl\RootResource $rootResource,
        \Lof\MarketPermissions\Model\ResourceModel\Permission\Collection $collection,
        \Magento\Framework\Acl\AclResource\ProviderInterface $resourceProvider,
        \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement,
        \Lof\MarketPermissions\Model\SellerUser $sellerUser
    ) {
        $this->rootResource = $rootResource;
        $this->collection = $collection;
        $this->resourceProvider = $resourceProvider;
        $this->roleManagement = $roleManagement;
        $this->sellerUser = $sellerUser;
    }

    /**
     * Populate ACL with rules from external storage.
     *
     * @param Acl $acl
     * @return void
     */
    public function populateAcl(Acl $acl)
    {
        $reverseAclArray = $this->getReverseAclArray($this->resourceProvider->getAclResources());
        $processedResources = [];
        $this->collection->addFieldToFilter(
            \Lof\MarketPermissions\Api\Data\PermissionInterface::ROLE_ID,
            ['in' => $this->getSellerRolesIds()]
        );
        $permissions = $this->collection->getItems();

        /** @var \Lof\MarketPermissions\Api\Data\PermissionInterface $rule */
        foreach ($permissions as $rule) {
            $roleId = $rule->getRoleId();
            $resource = $rule->getResourceId();
            if (!isset($processedResources[$roleId])) {
                $processedResources[$roleId] = [];
            }
            $this->hydrateAclByResource($acl, $rule, $resource, $roleId, $processedResources, $reverseAclArray);
        }
    }

    /**
     * Gets revers acl array.
     *
     * @param array $aclArray
     * @param array $parents
     * @param array $return
     * @return array
     */
    private function getReverseAclArray(array $aclArray, array $parents = [], array &$return = [])
    {
        foreach ($aclArray as $item) {
            if (isset($item['children']) && count($item['children'])) {
                $this->getReverseAclArray($item['children'], array_merge($parents, [$item['id']]), $return);
            } else {
                $return[$item['id']] = $parents;
            }
        }
        return $return;
    }

    /**
     * Hydrate Acl with rules only if Acl has each resource
     * @param Acl $acl
     * @param Permission $rule
     * @param string $resource
     * @param int $roleId
     * @param array $processedResources
     * @param array $reverseAclArray
     * @return void
     */
    private function hydrateAclByResource(
        Acl $acl,
        Permission $rule,
        $resource,
        $roleId,
        array &$processedResources,
        array $reverseAclArray
    ) {
        if ($acl->has($resource) && !in_array($resource, $processedResources[$roleId])) {
            if ($rule->getPermission() == 'allow') {
                if ($resource === $this->rootResource->getId()) {
                    $acl->allow($roleId, null);
                }
                $acl->allow($roleId, $resource);
                $processedResources[$roleId][] = $resource;
                if (isset($reverseAclArray[$resource])) {
                    foreach ($reverseAclArray[$resource] as $reverseAclArrayItem) {
                        $acl->allow($roleId, $reverseAclArrayItem);
                        $processedResources[$roleId][] = $reverseAclArrayItem;
                    }
                }
            } elseif ($rule->getPermission() == 'deny') {
                $acl->deny($roleId, $resource);
                $processedResources[$roleId][] = $resource;
            }
        }
    }

    /**
     * Get IDs of all seller roles.
     *
     * @return array
     */
    private function getSellerRolesIds()
    {
        $roles = $this->roleManagement->getRolesBySellerId($this->sellerUser->getCurrentSellerId());
        return array_map(
            function ($role) {
                return $role->getId();
            },
            $roles
        );
    }
}
