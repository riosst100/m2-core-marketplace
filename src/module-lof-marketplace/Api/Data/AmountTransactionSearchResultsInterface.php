<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface AmountTransactionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get AmountTransaction list.
     * @return \Lof\MarketPlace\Api\Data\AmountTransactionInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Lof\MarketPlace\Api\Data\AmountTransactionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

