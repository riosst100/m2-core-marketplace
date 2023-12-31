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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Api\Data;

interface QuoteSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Quote list.
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface[]
     */
    public function getItems();

    /**
     * Set entity_id list.
     * @param \Lofmp\SplitCart\Api\Data\QuoteInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
