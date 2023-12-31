<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\FeaturedProducts\Api\Data;

interface FeaturedProductSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get FeaturedProduct list.
     * @return \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Lofmp\FeaturedProducts\Api\Data\FeaturedProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
