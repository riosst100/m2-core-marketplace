<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\FeaturedProducts\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FeaturedProductRepositoryInterface
{

    /**
     * Save FeaturedProduct
     * @param $customerId
     * @param \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface $featuredProduct
     * @return \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveFeatureProduct(
        int $customerId,
        \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface $featuredProduct
    );

    /**
     * Retrieve FeaturedProduct
     * @param int $id
     * @param int $customerId
     * @return \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(int $customerId, $id);

    /**
     * Retrieve FeaturedProduct matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\FeaturedProducts\Api\Data\FeaturedProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getListFeatureProduct(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete FeaturedProduct
     * @param \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface $featuredProduct
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface $featuredProduct
    );

    /**
     * Delete FeaturedProduct by ID
     * @param int $id
     * @param int $customerId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $customerId, $id);
}

