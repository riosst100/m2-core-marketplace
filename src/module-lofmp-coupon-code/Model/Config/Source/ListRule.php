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
 
class ListRule implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_couponHelper;

    /**
     * @param \Lofmp\CouponCode\Helper\Data $couponHelper
     */
    public function __construct(
    	\Lofmp\CouponCode\Helper\Data $couponHelper
    	) {
    	$this->_couponHelper = $couponHelper;
    }
    public function toOptionArray()
    {
        $collection = $this->_couponHelper->getAllRule();
    	$rules = array();
    	foreach ($collection as $key=>$val) {
    		$rules[] = [
    		'value' => $key,
    		'label' => addslashes($val)
    		];
    	}
        array_unshift($rules, array(
                'value' => '',
                'label' => __('-- Please Select A Rule --'),
                ));
        return $rules;
    }
}