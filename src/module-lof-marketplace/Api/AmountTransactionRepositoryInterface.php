<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface AmountTransactionRepositoryInterface
{
    /**
     * GET seller transactions
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerTransactions(int $customerId, SearchCriteriaInterface $searchCriteria);

    /**
     * Save AmountTransaction
     * @param \Lof\MarketPlace\Api\Data\AmountTransactionInterface $amountTransaction
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\AmountTransactionInterface $amountTransaction
    );

    /**
     * Retrieve AmountTransaction
     * @param int $id
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve AmountTransaction matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete AmountTransaction
     * @param \Lof\MarketPlace\Api\Data\AmountTransactionInterface $amountTransaction
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\AmountTransactionInterface $amountTransaction
    );

    /**
     * Delete AmountTransaction by ID
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}

