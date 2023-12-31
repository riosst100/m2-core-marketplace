<?php
/**
 * Copyright © teads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface WithdrawalSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Withdrawal list.
     * @return \Lof\MarketPlace\Api\Data\WithdrawalInterface[]
     */
    public function getItems();

    /**
     * Set seller_id list.
     * @param \Lof\MarketPlace\Api\Data\WithdrawalInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

