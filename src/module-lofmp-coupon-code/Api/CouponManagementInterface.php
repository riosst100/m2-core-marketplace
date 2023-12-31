<?php
/**
 * Lof CouponCode is a powerful tool for managing the processing return and exchange requests within your workflow. This, in turn, allows your customers to request and manage returns and exchanges directly from your webstore. The Extension compatible with magento 2.x
 * Copyright (C) 2017  Landofcoder.com
 *
 * This file is part of Lofmp/CouponCode.
 *
 * Lofmp/CouponCode is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Lofmp\CouponCode\Api;

interface CouponManagementInterface
{
    /**
     * GET for Coupon api
     * @param int $customerId
     * @param string $alias
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function getCouponAlias($customerId, $alias);

    /**
     * Save coupon
     * @param int $customerId
     * @param \Lofmp\CouponCode\Api\Data\CouponInterface $Ticket
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function putCoupon($customerId, \Lofmp\CouponCode\Api\Data\CouponInterface $coupon);

    /**
     * Retrieve Coupon matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCouponByConditions($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Expired Coupon matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExpiredCoupons($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Used Coupon matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUsedCoupons($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Available Coupon matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAvailableCoupons($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Public Coupons matching the specified criteria.
     * @param string $sellerUrl
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPublicCoupons(string $sellerUrl, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve All Public Coupons matching the specified criteria.
     * @param string $type
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllPublicCoupons(string $type, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve Coupon matching email address.
     * @param int $customerId
     * @param string $email
     * @param int $page
     * @param int $limit
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCouponByEmail($customerId, $email, $page = 1, $limit = 20);

    /**
     * Retrieve Coupon matching by rule id
     * @param int $customerId
     * @param int $ruleId
     * @param int $page
     * @param int $limit
     * @return \Lofmp\CouponCode\Api\Data\CouponSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCouponByRuleId($customerId, $ruleId, $page = 1, $limit = 20);

    /**
     * Delete Coupon
     * @param int $customerId
     * @param \Lofmp\CouponCode\Api\Data\CouponInterface $coupon
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        $customerId,
        \Lofmp\CouponCode\Api\Data\CouponInterface $coupon
    );

    /**
     * Delete Coupon by ID
     * @param int $customerId
     * @param string $couponId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId, $couponId);
}
