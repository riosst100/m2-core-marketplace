<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Model\Config\Source;

class Layout
{
    /**
     * Layout getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [
                    ['value' => '0', 'label' => __('Grid')],
                    ['value' => '1', 'label' => __('List')],
                    ['value' => '2', 'label' => __('Slider')],
                ];

        return $data;
    }
}
