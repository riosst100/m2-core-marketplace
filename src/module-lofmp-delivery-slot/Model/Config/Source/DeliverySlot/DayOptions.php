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

namespace Lofmp\DeliverySlot\Model\Config\Source\DeliverySlot;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class DayOptions
 * @package Lofmp\DeliverySlot\Model\Config\Source\DeliverySlot
 */
class DayOptions implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Monday',
                'value' => 'mon'
            ],
            1 => [
                'label' => 'Tuesday',
                'value' => 'tue'
            ],
            2 => [
                'label' => 'Wednesday',
                'value' => 'wed'
            ],
            3 => [
                'label' => 'Thursday',
                'value' => 'thu'
            ],
            4 => [
                'label' => 'Friday',
                'value' => 'fri'
            ],
            5 => [
                'label' => 'Saturday',
                'value' => 'sat'
            ],
            6 => [
                'label' => 'Sunday',
                'value' => 'sun'
            ]
        ];

        return $options;
    }
}
