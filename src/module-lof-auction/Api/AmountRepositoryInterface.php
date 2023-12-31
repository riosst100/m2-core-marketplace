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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface AmountRepositoryInterface
 * @package Lof\Auction\Api
 */
interface AmountRepositoryInterface
{

    /**
     * Save Amount
     * @param \Lof\Auction\Api\Data\AmountInterface $amount
     * @return \Lof\Auction\Api\Data\AmountInterface
     * @throws LocalizedException
     */
    public function save(\Lof\Auction\Api\Data\AmountInterface $amount);

    /**
     * Retrieve Amount
     * @param string $entityId
     * @return \Lof\Auction\Api\Data\AmountInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve Amount matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\AmountSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Amount matching the specified criteria.
     * @param int $entityId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\AmountSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByAuctionId(
        $entityId,
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Amount
     * @param int $entityId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete($entityId);

    /**
     * Delete Amount by ID
     * @param int $entityId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($entityId);

    /**
     * Save Amount
     * @param int|null $customerId
     * @param \Lof\Auction\Api\Data\AmountInterface $amount
     * @return \Lof\Auction\Api\Data\AmountInterface
     * @throws LocalizedException
     */
    public function saveBid($customerId, \Lof\Auction\Api\Data\AmountInterface $amount);

    /**
     * Save Amount
     * @param int|null $customerId
     * @param \Lof\Auction\Api\Data\AmountInterface $amount
     * @return \Lof\Auction\Api\Data\AmountInterface
     * @throws LocalizedException
     */
    public function placeManualBid($customerId, \Lof\Auction\Api\Data\AmountInterface $amount);

    /**
     * Retrieve AutoAuction matching the specified criteria.
     * @param int|null $customerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\AmountSearchResultsInterface
     * @throws LocalizedException
     */
    public function getMyList(
        $customerId,
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $customerId
     * @param int $productId
     * @param float|int $amount
     * @param bool $isAuto
     * @return mixed
     */
    public function placeBid(
        $customerId,
        $productId,
        $amount,
        $isAuto
    );
}
