<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\FeaturedProducts\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GetFeaturedProductRepositoryInterface
{
    /**
     * Retrieve seller featured Products matching the specified criteria.
     * @param string $sellerUrl
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(
        string $sellerUrl,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}

