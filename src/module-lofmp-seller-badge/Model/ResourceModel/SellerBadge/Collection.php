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

namespace Lofmp\SellerBadge\Model\ResourceModel\SellerBadge;

class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'badge_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Lofmp\SellerBadge\Model\SellerBadge::class,
            \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge::class
        );
    }
}
