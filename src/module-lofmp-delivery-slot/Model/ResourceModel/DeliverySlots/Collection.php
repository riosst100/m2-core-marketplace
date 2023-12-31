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

namespace Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'slot_id';
    
    public function _construct()
    {
        $this->_init(
            \Lofmp\DeliverySlot\Model\DeliverySlots::class,
            \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots::class
        );
    }
}
