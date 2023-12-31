<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SellerInvoice\Api\Data;

interface SellerinvoiceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get sellerinvoice list.
     * @return \Lof\SellerInvoice\Api\Data\SellerinvoiceInterface[]
     */
    public function getItems();

    /**
     * Set sellerinvoice_id list.
     * @param \Lof\SellerInvoice\Api\Data\SellerinvoiceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

