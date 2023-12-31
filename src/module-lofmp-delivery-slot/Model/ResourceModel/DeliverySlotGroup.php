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

namespace Lofmp\DeliverySlot\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class DeliverySlotGroup
 * @package Lofmp\DeliverySlot\Model\ResourceModel
 */
class DeliverySlotGroup extends AbstractDb
{
    /**
     * Define main table
     */
    public function _construct()
    {
        $this->_init('delivery_slot_group', 'group_id');
    }
}
