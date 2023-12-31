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

/**
 * Interface MixBidSearchResultsInterface
 * @package Lof\Auction\Api\Data
 */
interface MixBidSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Bid list.
     * @return \Lof\Auction\Api\Data\MixBidInterface[]
     */
    public function getItems();

    /**
     * Set Bid list.
     * @param \Lof\Auction\Api\Data\MixBidInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
