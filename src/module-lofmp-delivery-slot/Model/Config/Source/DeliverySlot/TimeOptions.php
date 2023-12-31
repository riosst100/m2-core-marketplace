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
 * Class Options
 * @package Lofmp\DeliverySlot\Model\Config\Source\DeliverySlot
 */
class TimeOptions implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $time = '';
        $options = [];
        for ($hours = 0; $hours < 24; $hours++) { // the interval for hours is '1'
            for ($mins = 0; $mins < 60; $mins += 30) {// the interval for mins is '30'
                $time .= '<option name =' . $hours . $mins . '>' . str_pad($hours, 2, '0', STR_PAD_LEFT) . ':'
                    . str_pad($mins, 2, '0', STR_PAD_LEFT) . '</option>';
                $options[] = [
                    'label' => str_pad($hours, 2, '0', STR_PAD_LEFT) . ':'
                        . str_pad($mins, 2, '0', STR_PAD_LEFT),
                    'value' => str_pad($hours, 2, '0', STR_PAD_LEFT) . ':'
                        . str_pad($mins, 2, '0', STR_PAD_LEFT),

                ];
            }
        }

        return $options;
    }
}
