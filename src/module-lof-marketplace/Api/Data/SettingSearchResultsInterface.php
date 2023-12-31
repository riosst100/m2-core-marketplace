<?php
/**
 * Copyright © asdfasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface SettingSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Setting list.
     * @return \Lof\MarketPlace\Api\Data\SettingInterface[]
     */
    public function getItems();

    /**
     * Set seller_id list.
     * @param \Lof\MarketPlace\Api\Data\SettingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

