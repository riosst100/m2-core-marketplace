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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\Quickrfq\Api\Data;

/**
 * Interface MessageSearchResultsInterface
 * @package Lof\Quickrfq\Api\Data
 */
interface MessageSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Message list.
     * @return \Lof\Quickrfq\Api\Data\MessageInterface[]
     */
    public function getItems();

    /**
     * Set Message list.
     * @param \Lof\Quickrfq\Api\Data\MessageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
