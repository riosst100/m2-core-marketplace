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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MaxAbsenteeBidSearchResultsInterface
 * @package Lof\Auction\Api\Data
 */
interface MaxAbsenteeBidSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get MaxAbsenteeBidInterface list.
     * @return \Lof\Auction\Api\Data\MaxAbsenteeBidInterface[]
     */
    public function getItems();

    /**
     * Set MaxAbsenteeBidInterface list.
     * @param \Lof\Auction\Api\Data\MaxAbsenteeBidInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
