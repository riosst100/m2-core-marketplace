<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Model\Config\Source;
 
class SimpleAction implements \Magento\Framework\Option\ArrayInterface
{
    protected function getListOptions()
    {
        return [
            \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION => __('Percent of product price discount'),
            \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION => __('Fixed amount discount'),
            //\Magento\SalesRule\Model\Rule::CART_FIXED_ACTION => __('Fixed amount discount for whole cart'),
            //\Magento\SalesRule\Model\Rule::BUY_X_GET_Y_ACTION => __('Buy X get Y free (discount amount is Y)')
        ];
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {   
        $listOptions = $this->getListOptions();
        $options = [];
        foreach ( $listOptions as $value => $label ) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
