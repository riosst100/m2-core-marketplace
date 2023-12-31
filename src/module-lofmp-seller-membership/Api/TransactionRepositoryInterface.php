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

interface TransactionRepositoryInterface
{
    /**
     * Save SellerMembership transaction
     * @param \Lofmp\SellerMembership\Api\Data\TransactionInterface $transaction
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\SellerMembership\Api\Data\TransactionInterface $transaction
    );

    /**
     * Retrieve SellerMembership transaction
     * @param string $transactionId
     * @return \Lofmp\SellerMembership\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($transactionId);

    /**
     * Retrieve SellerMembership matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SellerMembership\Api\Data\TransactionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SellerMembership transaction
     * @param \Lofmp\SellerMembership\Api\Data\TransactionInterface $transaction
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\SellerMembership\Api\Data\TransactionInterface $transaction
    );

    /**
     * Delete SellerMembership transaction by ID
     * @param string $transactionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($transactionId);
}
