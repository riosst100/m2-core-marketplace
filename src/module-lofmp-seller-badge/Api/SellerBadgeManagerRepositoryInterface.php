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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Api;

interface SellerBadgeManagerRepositoryInterface
{

    /**
     * Save SellerBadgeManager
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface $sellerBadgeManager
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface $sellerBadgeManager
    );

    /**
     * Retrieve SellerBadgeManager
     * @param string $managerId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($managerId);

    /**
     * Retrieve SellerBadgeManager matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SellerBadgeManager
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface $sellerBadgeManager
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface $sellerBadgeManager
    );

    /**
     * Delete SellerBadgeManager by ID
     * @param string $managerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($managerId);
}
