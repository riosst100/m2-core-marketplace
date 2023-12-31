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

interface ProductMembershipSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ProductMembershipInterface list.
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface[]
     */
    public function getItems();

    /**
     * Set ProductMembershipInterface list.
     * @param \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface[] $items
     * @return $this
     */
    public function setItems($items);
}
