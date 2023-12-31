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
 * Interface AutoAuctionRepositoryInterface
 * @package Lof\Auction\Api
 */
interface AutoAuctionRepositoryInterface
{
    /**
     * Save AutoAmount
     * @param \Lof\Auction\Api\Data\AutoAuctionInterface $autoauction
     * @return \Lof\Auction\Api\Data\AutoAuctionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($autoauction);

    /**
     * Retrieve AutoAmount
     * @param string $entityId
     * @return \Lof\Auction\Api\Data\AutoAuctionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve AutoAmount matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\AutoAuctionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Amount matching the specified criteria.
     * @param int $entityId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\AutoAuctionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByAuctionId(
        $entityId,
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete AutoAuction
     * @param int $entityId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete($entityId);

    /**
     * Delete AutoAuction by ID
     * @param int $entityId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($entityId);

    /**
     * Save AutoAuction
     * @param int|null $customerId
     * @param \Lof\Auction\Api\Data\AutoAuctionInterface $autoauction
     * @return \Lof\Auction\Api\Data\AutoAuctionInterface
     * @throws LocalizedException
     */
    public function saveAutoBid($customerId, $autoauction);

    /**
     * Place AutoAuction
     * @param int $customerId
     * @param int $productId
     * @return \Lof\Auction\Api\Data\AutoAuctionInterface
     * @throws LocalizedException
     */
    public function placeAutoBid($customerId, int $productId);

    /**
     * Retrieve AutoAuction matching the specified criteria.
     * @param int|null $customerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\AutoAuctionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getMyList(
        $customerId,
        SearchCriteriaInterface $searchCriteria
    );
}
