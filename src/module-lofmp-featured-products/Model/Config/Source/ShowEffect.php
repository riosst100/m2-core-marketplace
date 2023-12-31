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
 * @package    Lofmp_FeaturedProducts
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\FeaturedProducts\Model\Config\Source;

class ShowEffect implements \Magento\Framework\Option\ArrayInterface {

    /**
     * @var array
     */
    protected $optionArray;

    /**
     *
     */
    public function __construct(){
        $this->optionArray = [
            ['value' => 'collapse', 'label' => __('Collapse')],
            ['value' => 'slider', 'label' => __('Slider')]
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        return $this->optionArray;
    }
}