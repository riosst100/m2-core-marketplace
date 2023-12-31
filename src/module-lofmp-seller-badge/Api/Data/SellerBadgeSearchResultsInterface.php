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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Api\Data;

interface SellerBadgeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get SellerBadge list.
     * @return \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
