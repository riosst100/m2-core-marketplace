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
 * A repository class for role entity that provides basic CRUD operations.
 */
class RoleRepository implements \Lof\MarketPermissions\Api\RoleRepositoryInterface
{

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleInterface[]
     */
    private $instances = [];

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory
     */
    private $roleFactory;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Role
     */
    private $roleResource;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory
     */
    private $roleCollectionFactory;

    /**
     * @var \Lof\MarketPermissions\Api\Data\RoleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Lof\MarketPermissions\Model\Role\Permission
     */
    private $rolePermission;

    /**
     * @var \Lof\MarketPermissions\Model\PermissionManagementInterface
     */
    private $permissionManagement;

    /**
     * @var \Lof\MarketPermissions\Model\Role\Validator
     */
    private $validator;

    /**
     * @param \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory $roleFactory
     * @param \Lof\MarketPermissions\Model\ResourceModel\Role $roleResource
     * @param \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
     * @param \Lof\MarketPermissions\Api\Data\RoleSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Lof\MarketPermissions\Model\Role\Permission $rolePermission
     * @param \Lof\MarketPermissions\Model\PermissionManagementInterface $permissionManagement
     *
     */
    public function __construct(
        \Lof\MarketPermissions\Api\Data\RoleInterfaceFactory $roleFactory,
        \Lof\MarketPermissions\Model\ResourceModel\Role $roleResource,
        \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory,
        \Lof\MarketPermissions\Api\Data\RoleSearchResultsInterfaceFactory $searchResultsFactory,
        \Lof\MarketPermissions\Model\Role\Permission $rolePermission,
        \Lof\MarketPermissions\Model\PermissionManagementInterface $permissionManagement,
        \Lof\MarketPermissions\Model\Role\Validator $validator
    ) {
        $this->validator = $validator;
        $this->roleFactory = $roleFactory;
        $this->roleResource = $roleResource;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->rolePermission = $rolePermission;
        $this->permissionManagement = $permissionManagement;
    }

    /**
     * @inheritdoc
     */
    public function save(\Lof\MarketPermissions\Api\Data\RoleInterface $role)
    {
        $role = $this->validator->retrieveRole($role);
        $allowedResources = $this->permissionManagement->retrieveAllowedResources($role->getPermissions());
        $permissions = $this->permissionManagement->populatePermissions($allowedResources);
        $this->validator->validatePermissions($permissions, $allowedResources);
        $role->setPermissions($permissions);
        if ($this->validateRoleName($role) === false) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__(
                'User role with this name already exists. Enter a different name to save this role.'
            ));
        }
        $this->roleResource->save($role);
        $this->rolePermission->saveRolePermissions($role);
        unset($this->instances[$role->getId()]);

        return $role;
    }

    /**
     * Validate that role name is unique.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface $role
     * @return bool
     */
    private function validateRoleName(\Lof\MarketPermissions\Api\Data\RoleInterface $role)
    {
        $collection = $this->roleCollectionFactory->create();
        $collection->addFieldToFilter(
            \Lof\MarketPermissions\Api\Data\RoleInterface::ROLE_NAME,
            ['eq' => $role->getRoleName()]
        );
        $collection->addFieldToFilter(
            \Lof\MarketPermissions\Api\Data\RoleInterface::SELLER_ID,
            ['eq' => $role->getSellerId()]
        );

        if ($role->getId()) {
            $collection->addFieldToFilter(
                \Lof\MarketPermissions\Api\Data\RoleInterface::ROLE_ID,
                ['neq' => $role->getId()]
            );
        }

        return !$collection->getSize();
    }

    /**
     * @inheritdoc
     */
    public function get($roleId)
    {
        if (!isset($this->instances[$roleId])) {
            /** @var \Lof\MarketPermissions\Api\Data\RoleInterface $role */
            $role = $this->roleFactory->create();
            $this->roleResource->load($role, $roleId);
            $this->validator->checkRoleExist($role, $roleId);
            $role->setPermissions($this->rolePermission->getRolePermissions($role));
            $this->instances[$roleId] = $role;
        }
        return $this->instances[$roleId];
    }

    /**
     * @inheritdoc
     */
    public function delete($roleId)
    {
        $role = $this->get($roleId);
        $this->validator->validateRoleBeforeDelete($role);
        try {
            $this->roleResource->delete($role);
            $this->rolePermission->deleteRolePermissions($role);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
                __(
                    'Cannot delete role with id %1',
                    $role->getId()
                ),
                $e
            );
        }
        unset($this->instances[$roleId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->roleCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    $sortOrder->getDirection()
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $items = $collection->getItems();

        foreach ($items as $itemKey => $itemValue) {
            $items[$itemKey]->setPermissions($this->rolePermission->getRolePermissions($itemValue));
        }

        $searchResults->setItems($items);
        return $searchResults;
    }
}
