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
 * Interface for basic CRUD operations for team entity.
 */
interface TeamRepositoryInterface
{
    /**
     * Create a team in the seller structure.
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamInterface $team
     * @param int $sellerId
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function create(\Lof\MarketPermissions\Api\Data\TeamInterface $team, $sellerId);

    /**
     * Update a team in the seller structure.
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamInterface $team
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Lof\MarketPermissions\Api\Data\TeamInterface $team);

    /**
     * Returns data for a team in the seller, by entity id.
     *
     * @param int $teamId
     * @return \Lof\MarketPermissions\Api\Data\TeamInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($teamId);

    /**
     * Delete team.
     *
     * @param \Lof\MarketPermissions\Api\Data\TeamInterface $team
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Lof\MarketPermissions\Api\Data\TeamInterface $team);

    /**
     * Delete a team from the seller structure.
     *
     * @param int $teamId
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($teamId);

    /**
     * Returns the list of teams for the specified search criteria (team name or description).
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPermissions\Api\Data\TeamSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \InvalidArgumentException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
