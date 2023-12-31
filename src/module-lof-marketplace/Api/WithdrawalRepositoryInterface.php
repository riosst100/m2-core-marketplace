<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface WithdrawalRepositoryInterface
{

    /**
     * Save Withdrawal
     * @param \Lof\MarketPlace\Api\Data\WithdrawalInterface $withdrawal
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\WithdrawalInterface $withdrawal
    );

    /**
     * Request Withdrawal
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\WithdrawalInterface $withdrawal
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestWithdrawal(
        int $customerId,
        \Lof\MarketPlace\Api\Data\WithdrawalInterface $withdrawal
    );

    /**
     * Retrieve Withdrawal
     * @param string $withdrawalId
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($withdrawalId);

    /**
     * Retrieve Withdrawal matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\WithdrawalSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Withdrawal matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\WithdrawalSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Withdrawal
     * @param \Lof\MarketPlace\Api\Data\WithdrawalInterface $withdrawal
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\WithdrawalInterface $withdrawal
    );

    /**
     * Delete Withdrawal by ID
     * @param string $withdrawalId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($withdrawalId);
}

