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
 * A repository interface for role entity that provides basic CRUD operations.
 */
interface RoleRepositoryInterface
{
    /**
     * Returns the list of roles and permissions for a specified seller.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPermissions\Api\Data\RoleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Create or update a role for a selected seller.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface $role
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save(\Lof\MarketPermissions\Api\Data\RoleInterface $role);

    /**
     * Delete a role.
     *
     * @param int $roleId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete($roleId);

    /**
     * Returns the list of permissions for a specified role.
     *
     * @param int $roleId
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($roleId);
}
