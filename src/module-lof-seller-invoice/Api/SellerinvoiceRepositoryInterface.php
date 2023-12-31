<?php

/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lof\SellerInvoice\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SellerinvoiceRepositoryInterface
{

    /**
     * Retrieve sellerinvoice matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\SellerInvoice\Api\Data\SellerinvoiceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetListInvoice(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param int $orderId
     * @param int $customerId
     * @return \Lof\SellerInvoice\Api\Data\DataInvoiceInterface
     */
    public function sellerGetInvoice(
        int $customerId,
        int $orderId
    );
}
