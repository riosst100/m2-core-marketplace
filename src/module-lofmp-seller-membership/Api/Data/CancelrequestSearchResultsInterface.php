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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Api\Data;

interface CancelrequestSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get cancel list.
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface[]
     */
    public function getItems();

    /**
     * Set cancel list.
     * @param \Lofmp\SellerMembership\Api\Data\CancelrequestInterface[] $items
     * @return $this
     */
    public function setItems($items);
}
