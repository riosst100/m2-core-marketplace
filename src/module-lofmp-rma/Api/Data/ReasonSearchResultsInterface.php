<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Api\Data;

interface ReasonSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get rmas list.
     *
     * @return \Lofmp\Rma\Api\Data\ReasonInterface[]
     */
    public function getItems();

    /**
     * Set rmas list.
     *
     * @param array $items Array of \Lofmp\Rma\Api\Data\ReasonInterface
     * @return $this
     */
    public function setItems(array $items);
}
