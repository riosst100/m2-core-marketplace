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
 * A repository interface for seller entity that provides basic CRUD operations.
 */
interface SellerRepositoryInterface
{
    /**
     * Create or update a seller account.
     *
     * @param \Lof\MarketPermissions\Api\Data\SellerInterface $seller
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function save(\Lof\MarketPermissions\Api\Data\SellerInterface $seller);

    /**
     * Returns seller details.
     *
     * @param int $sellerId
     * @return \Lof\MarketPermissions\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($sellerId);

    /**
     * Removes seller entity and all the related links from the system.
     *
     * @param \Lof\MarketPermissions\Api\Data\SellerInterface $seller
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Lof\MarketPermissions\Api\Data\SellerInterface $seller);

    /**
     * Delete a seller. Customers belonging to a seller are not deleted with this request.
     *
     * @param int $sellerId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($sellerId);

    /**
     * Returns the list of companies. The list is an array of objects, and detailed information about item attributes
     * might not be included.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPermissions\Api\Data\SellerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
