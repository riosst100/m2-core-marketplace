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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Api;

interface MembershipRepositoryInterface
{
    /**
     * Save SellerMembership membership
     * @param \Lofmp\SellerMembership\Api\Data\MembershipInterface $membership
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\SellerMembership\Api\Data\MembershipInterface $membership
    );

    /**
     * Retrieve SellerMembership Membership
     * @param string $membershipId
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($membershipId);

    /**
     * Retrieve SellerMembership matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SellerMembership\Api\Data\MembershipSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SellerMembership membership
     * @param \Lofmp\SellerMembership\Api\Data\MembershipInterface $membership
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\SellerMembership\Api\Data\MembershipInterface $membership
    );

    /**
     * Delete SellerMembership membership by ID
     * @param string $membershipId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($membershipId);

    /**
     * Retrieve ProductMembership
     * @param int $customerId
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyMembership(
        $customerId
    );
}
