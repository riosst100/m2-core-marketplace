<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PaymentRepositoryInterface
{

    /**
     * Save Payment
     * @param \Lof\MarketPlace\Api\Data\PaymentInterface $payment
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\PaymentInterface $payment
    );

    /**
     * Retrieve Payment
     * @param string $paymentId
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($paymentId);

    /**
     * Retrieve Payment matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\PaymentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Payment matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\PaymentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPublicList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );


    /**
     * Delete Payment
     * @param \Lof\MarketPlace\Api\Data\PaymentInterface $payment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lof\MarketPlace\Api\Data\PaymentInterface $payment
    );

    /**
     * Delete Payment by ID
     * @param string $paymentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($paymentId);
}

