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
 * @package    Lof_RewardPoints
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Model\ResourceModel\Log;

use Lofmp\CouponCode\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{

	protected function _afterLoad()
    {
        return parent::_afterLoad();
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\CouponCode\Model\Log', 'Lofmp\CouponCode\Model\ResourceModel\Log');
    }
}