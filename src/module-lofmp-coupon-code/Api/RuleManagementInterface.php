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

interface RuleManagementInterface
{

    /**
     * Save Rule
     * @param int $customerId
     * @param \Lofmp\CouponCode\Api\Data\RuleInterface $rule
     * @return \Lofmp\CouponCode\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    // public function save(\Lofmp\CouponCode\Api\Data\RuleInterface $rule);
    public function save($customerId, \Lofmp\CouponCode\Api\Data\RuleInterface $rule);

    /**
     * Retrieve Rule
     * @param int $customerId
     * @param int $ruleId
     * @return \Lofmp\CouponCode\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId, $ruleId);

    /**
     * Retrieve Rule matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\CouponCode\Api\Data\RuleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Rule
     * @param int $customerId
     * @param \Lofmp\CouponCode\Api\Data\RuleInterface $rule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        $customerId,
        \Lofmp\CouponCode\Api\Data\RuleInterface $rule
    );

    /**
     * Delete Rule by ID
     * @param int $customerId
     * @param int $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId, $ruleId);
}
