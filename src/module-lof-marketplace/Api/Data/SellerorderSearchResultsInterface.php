<?php
/**
 * Copyright © ads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface SellerorderSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Sellerorder list.
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface[]
     */
    public function getItems();

    /**
     * Set order_id list.
     * @param \Lof\MarketPlace\Api\Data\SellerorderInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

