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
 * Interface for role search results.
 *
 * @api
 * @since 100.0.0
 */
interface RoleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get roles list.
     *
     * @return \Lof\MarketPermissions\Api\Data\RoleInterface[]
     */
    public function getItems();

    /**
     * Set roles list.
     *
     * @param \Lof\MarketPermissions\Api\Data\RoleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
