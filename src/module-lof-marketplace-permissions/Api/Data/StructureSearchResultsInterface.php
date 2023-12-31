<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for seller structure search results
 */
interface StructureSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get structures list
     *
     * @return \Lof\MarketPermissions\Api\Data\StructureInterface[]
     */
    public function getItems();

    /**
     * Set structures list
     *
     * @param \Lof\MarketPermissions\Api\Data\StructureInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
