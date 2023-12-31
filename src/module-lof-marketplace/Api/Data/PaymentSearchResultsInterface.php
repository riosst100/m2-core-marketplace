<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface PaymentSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Payment list.
     * @return \Lof\MarketPlace\Api\Data\PaymentInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Lof\MarketPlace\Api\Data\PaymentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

