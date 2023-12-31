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

use Lof\Auction\Api\Data\MaxAbsenteeBidInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface MaxAbsenteeBidRepositoryInterface
 * @package Lof\Auction\Api
 */
interface MaxAbsenteeBidRepositoryInterface
{
    /**
     * Retrieve Amount matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Amount matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyList(
        $customerId,
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve next auto bid matching the specified criteria.
     * @param int $bid_id
     * @param bool $isAuto
     * @return bool|int|null
     * @throws LocalizedException
     */
    public function createAutoBid(
        $bid_id,
        $isAuto = true
    );

    /**
     * Retrieve MaxAbsenteeBid matching the specified criteria.
     * @param int $customerId
     * @param int $auction_id
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidInterface
     * @throws LocalizedException
     */
    public function getMaxBidAmount(
        $customerId,
        $auction_id
    );

    /**
     * Set MaxAbsenteeBid for customer
     * @param int $customerId
     * @param int $auction_id
     * @param float|int $max_bid_amount
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidInterface
     * @throws LocalizedException
     */
    public function setMaxBidAmount(
        $customerId,
        $auction_id,
        $max_bid_amount
    );

    /**
     * Flush MaxAbsenteeBid for customer when auction is win or lower min bid amount
     * @param int $auction_id
     * @return bool|int
     * @throws LocalizedException
     */
    public function flushLowerAbsenteeBid(
        $auction_id
    );

    /**
     * Flush MaxAbsenteeBid by customer id when his bid is spam
     * @param int $customer_id
     * @param int $auction_id
     * @return bool|int
     * @throws LocalizedException
     */
    public function flushByCustomer(
        $customer_id,
        $auction_id
    );

    /**
     * Flush MaxAbsenteeBid for customer
     * @param int $id
     * @return bool|int
     * @throws LocalizedException
     */
    public function delete(
        $id
    );

    /**
     * Flush MaxAbsenteeBid for customer
     * @param int $customer_id
     * @param int $id
     * @return bool|int
     * @throws LocalizedException
     */
    public function deleteMySubscription(
        $customer_id,
        $id
    );

    /**
     * Retrieve MaxAbsenteeBid
     * @param int $entityId
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);
}
