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

interface MembershipSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get membership list.
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface[]
     */
    public function getItems();

    /**
     * Set membership list.
     * @param \Lofmp\SellerMembership\Api\Data\MembershipInterface[] $items
     * @return $this
     */
    public function setItems($items);
}
