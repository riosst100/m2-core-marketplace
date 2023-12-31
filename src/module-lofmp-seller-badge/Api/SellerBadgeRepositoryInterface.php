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

interface SellerBadgeRepositoryInterface
{
    /**
     * Save SellerBadge
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface $sellerBadge
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface $sellerBadge
    );

    /**
     * Retrieve SellerBadge
     * @param string $badgeId
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($badgeId);

    /**
     * Retrieve SellerBadge matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SellerBadge
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface $sellerBadge
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface $sellerBadge
    );

    /**
     * Delete SellerBadge by ID
     * @param string $badgeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($badgeId);
}
