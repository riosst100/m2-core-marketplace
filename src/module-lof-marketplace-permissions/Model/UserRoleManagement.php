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

use Lof\MarketPermissions\Api\AclInterface;

/**
 * Management operations for user roles.
 */
class UserRoleManagement implements AclInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\UserRole\CollectionFactory
     */
    private $userRoleCollectionFactory;

    /**
     * @var \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory
     */
    private $roleCollectionFactory;

    /**
     * @var \Lof\MarketPermissions\Model\UserRoleFactory
     */
    private $userRoleFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

//    /**
//     * @var \Lof\MarketPermissions\Api\RoleRepositoryInterface
//     */
//    private $roleRepository;

    /**
     * @var \Lof\MarketPermissions\Api\RoleManagementInterface
     */
    private $roleManagement;

    /**
     * @var \Lof\MarketPermissions\Model\SellerAdminPermission
     */
    private $sellerAdminPermission;

    /**
     * @var \Magento\Framework\Acl\Data\CacheInterface
     */
    private $aclDataCache;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     *
     * @param \Lof\MarketPermissions\Model\ResourceModel\UserRole\CollectionFactory $userRoleCollectionFactory
     * @param \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
     * @param \Lof\MarketPermissions\Model\UserRoleFactory $userRoleFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement
     * @param \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission
     * @param \Magento\Framework\Acl\Data\CacheInterface $aclDataCache
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Lof\MarketPermissions\Model\ResourceModel\UserRole\CollectionFactory $userRoleCollectionFactory,
        \Lof\MarketPermissions\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory,
        \Lof\MarketPermissions\Model\UserRoleFactory $userRoleFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Api\RoleManagementInterface $roleManagement,
        \Lof\MarketPermissions\Model\SellerAdminPermission $sellerAdminPermission,
        \Magento\Framework\Acl\Data\CacheInterface $aclDataCache,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->userRoleCollectionFactory = $userRoleCollectionFactory;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->userRoleFactory = $userRoleFactory;
        $this->customerRepository = $customerRepository;
        $this->roleManagement = $roleManagement;
        $this->sellerAdminPermission = $sellerAdminPermission;
        $this->aclDataCache = $aclDataCache;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function assignRoles($userId, array $roles)
    {
        $customer = $this->customerRepository->getById($userId);
        $sellerId = $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId();
        $firstRole = reset($roles);
        if (empty($firstRole) || !$firstRole->getId()) {
            throw new \Magento\Framework\Exception\InputException(
                __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'id'])
            );
        }
        if (!$sellerId) {
            throw new \Magento\Framework\Exception\InputException(
                __(
                    'You cannot update the requested attribute. Row ID: %fieldName = %fieldValue.',
                    ['fieldName' => 'roleId', 'fieldValue' => $firstRole->getId()]
                )
            );
        } else {
            $this->validateDataBeforeAssignRoles($sellerId, $userId, $roles);
        }
        $userRoleCollection = $this->userRoleCollectionFactory->create();
        $userRoleCollection->addFieldToFilter('user_id', ['eq' => $userId])->load();
        $userRoles = $userRoleCollection->getItems();

        foreach ($roles as $roleKey => $userToAssignedRole) {
            foreach ($userRoles as $userRoleKey => $currentRole) {
                if ($userToAssignedRole->getId() == $currentRole->getRoleId()) {
                    unset($roles[$roleKey]);
                    unset($userRoles[$userRoleKey]);
                    break;
                }
            }
        }

        foreach ($userRoles as $userRole) {
            $userRole->delete();
        }

        foreach ($roles as $role) {
            $userRole = $this->userRoleFactory->create();
            $userRole->setRoleId($role->getId());
            $userRole->setUserId($userId);
            $userRole->save();
        }
        $this->aclDataCache->clean();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function assignUserDefaultRole($userId, $sellerId)
    {
        $role = $this->roleManagement->getSellerDefaultRole($sellerId);
        $this->assignRoles($userId, [$role]);
    }

    /**
     * @inheritdoc
     */
    public function getRolesByUserId($userId)
    {
        try {
            $isSellerAdmin = $this->sellerAdminPermission->isGivenUserSellerAdmin($userId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $isSellerAdmin = false;
        }

        if ($isSellerAdmin) {
            return [$this->roleManagement->getAdminRole()];
        }

        $userRoleCollection = $this->userRoleCollectionFactory->create();
        $userRoleCollection->addFieldToFilter('user_id', ['eq' => $userId])->load();
        $userRoles = $userRoleCollection->getItems();
        if (!count($userRoles)) {
            return [];
        }
        $roleIds = [];
        foreach ($userRoles as $userRole) {
            $roleIds[] = $userRole->getRoleId();
        }
        $roleCollection = $this->roleCollectionFactory->create();
        $roleCollection->addFieldToFilter('role_id', ['in' => $roleIds])->load();
        if (!$roleCollection->getSize()) {
            return [];
        }

        return $roleCollection->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getUsersByRoleId($roleId)
    {
        $this->roleRepository->get($roleId);
        $userRoleCollection = $this->userRoleCollectionFactory->create();
        $userRoleCollection->addFieldToFilter('role_id', ['eq' => $roleId])->load();
        $userIds = $userRoleCollection->getColumnValues('user_id');
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $userIds, 'in')
            ->create();
        return $this->customerRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @inheritDoc
     */
    public function getUsersCountByRoleId($roleId)
    {
        $userRoleCollection = $this->userRoleCollectionFactory->create();
        $userRoleCollection->addFieldToFilter('role_id', ['eq' => $roleId]);

        return (int)$userRoleCollection->getSize();
    }

    /**
     * @inheritdoc
     */
    public function deleteRoles($userId)
    {
        $userRoleCollection = $this->userRoleCollectionFactory->create();
        $userRoleCollection->addFieldToFilter('user_id', ['eq' => $userId])->load();
        $userRoles = $userRoleCollection->getItems();
        foreach ($userRoles as $userRole) {
            $userRole->delete();
        }
        $this->aclDataCache->clean();
    }

    /**
     * Validates data before change a role for a seller user.
     *
     * @param int $sellerId
     * @param int $userId
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface [] $roles
     * @throws \Magento\Framework\Exception\InputException
     * @return void
     */
    private function validateDataBeforeAssignRoles($sellerId, $userId, array $roles)
    {
        if ($this->sellerAdminPermission->isGivenUserSellerAdmin($userId)) {
            throw new \Magento\Framework\Exception\InputException(
                __('You cannot assign a different role to a seller admin.')
            );
        }
        $sellerIdRoles = $this->roleManagement->getRolesBySellerId($sellerId);
        $sellerIdRoleIds = $this->prepareRoleIds($sellerIdRoles);
        $assignedRoleId = $this->prepareRoleIds($roles);
        if (count($assignedRoleId) > 1) {
            throw new \Magento\Framework\Exception\InputException(
                __('You cannot assign multiple roles to a user.')
            );
        }
        if (array_diff($assignedRoleId, $sellerIdRoleIds)) {
            throw new \Magento\Framework\Exception\InputException(
                __(
                    'Invalid value of "%value" provided for the %fieldName field.',
                    ['fieldName' => 'role_id', 'value' => $assignedRoleId[0]]
                )
            );
        }
    }

    /**
     * Prepare role ids for validation, before change a role for a seller user.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface [] $roles
     * @return array
     */
    private function prepareRoleIds(array $roles)
    {
        $roleIds = [];
        /** @var \Lof\MarketPermissions\Api\Data\RoleInterface $role */
        foreach ($roles as $role) {
            $roleIds[] = $role->getId();
        }
        return $roleIds;
    }
}
