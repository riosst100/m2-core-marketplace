<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'group_id';

    /**
     * Define model & resource model
     */
    public function _construct()
    {
        $this->_init(
            \Lofmp\DeliverySlot\Model\DeliverySlotGroup::class,
            \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup::class
        );
    }
}
