<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\TimeDiscount\Api\Data;

interface ProductSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Product list.
     * @return \Lofmp\TimeDiscount\Api\Data\ProductInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Lofmp\TimeDiscount\Api\Data\ProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

