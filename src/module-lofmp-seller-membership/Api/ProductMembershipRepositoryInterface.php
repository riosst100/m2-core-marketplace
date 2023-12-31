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

interface ProductMembershipRepositoryInterface
{
    /**
     * Retrieve ProductMembership matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve ProductMembership
     * @param int $customerId
     * @param int|null $storeId
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCustomer(
        $customerId,
        $storeId = null
    );
}
