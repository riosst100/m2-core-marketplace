<?php
/**
 * Copyright © Landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface MessageRepositoryInterface
 */
interface MessageRepositoryInterface
{
    /**
     * @param int $sellerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketplaceGraphQl\Api\Data\MessageSearchResultsInterface
     */
    public function getListSellerMessages(
        int $sellerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $customerId
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketplaceGraphQl\Api\Data\MessageSearchResultsInterface
     */
    public function getListMessages(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}
