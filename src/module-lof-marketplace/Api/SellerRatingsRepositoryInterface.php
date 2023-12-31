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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Api;

interface SellerRatingsRepositoryInterface
{
    /**
     * GET seller ratings
     * @param int $seller_id
     * @return mixed
     */
    public function getSellerRatings($seller_id);

    /**
     * GET seller rating by id
     * @param int $id
     * @return mixed
     */
    public function getSellerRatingsById($id);

    /**
     * Retrieve Seller Ratings matching the specified criteria.
     * @param int $sellerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param bool $showEmail = false
     * @return \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        int $sellerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $showEmail = false
    );

    /**
     * Retrieve Seller Review matching the specified criteria.
     * @param string $sellerUrl - the url key of seller. ex: sellerA
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListByUrl(
        string $sellerUrl,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Seller Review matching the specified criteria.
     * @param string $sellerUrl - the url key of seller. ex: sellerA
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSummaryRatings(
        string $sellerUrl
    );

    /**
     * Retrieve Seller Review matching the specified criteria.
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\SummaryRatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSummaryRatingsBySellerId(
        int $sellerId
    );

    /**
     * Retrieve Seller Ratings matching the specified criteria.
     * @param int $sellerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Save Rating
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\RatingInterface $rating
     * @return \Lof\MarketPlace\Api\Data\RatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveMyRating(
        int $customerId,
        \Lof\MarketPlace\Api\Data\RatingInterface $rating
    );

    /**
     * Save Rating
     * @param \Lof\MarketPlace\Api\Data\RatingInterface $rating
     * @return \Lof\MarketPlace\Api\Data\RatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lof\MarketPlace\Api\Data\RatingInterface $rating
    );

    /**
     * Retrieve Seller Ratings matching the specified criteria.
     * @param int $ratingId
     * @return \Lof\MarketPlace\Api\Data\RatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(
        int $ratingId
    );

    /**
     * Retrieve Seller Ratings matching the specified criteria.
     * @param int $ratingId
     * @return \Lof\MarketPlace\Api\Data\RatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(
        int $ratingId
    );

    /**
     * Retrieve Seller Ratings matching the specified criteria.
     * @param int $customerId
     * @param int $ratingId
     * @return \Lof\MarketPlace\Api\Data\RatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMyRating(
        int $customerId,
        int $ratingId
    );

    /**
     * Retrieve Message matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRatingsList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Rating
     * @param \Lof\MarketPlace\Api\Data\RatingInterface $rating
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete (
        \Lof\MarketPlace\Api\Data\RatingInterface $rating
    );

    /**
     * Delete Rating by ID
     * @param int $ratingId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById (int $ratingId);

    /**
     * Plus/Minus/Report Rating
     * @param int $customerId
     * @param int $ratingId
     * @param string $type (accept value: plus|minus|report)
     * @param \Lof\MarketPlace\Api\Data\RatingInterface $rating
     * @return \Lof\MarketPlace\Api\Data\RatingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateRating (
        int $customerId,
        int $ratingId,
        string $type
    );
}
