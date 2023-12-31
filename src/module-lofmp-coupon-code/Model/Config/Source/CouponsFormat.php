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
 
class CouponsFormat implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\SalesRule\Helper\Coupon
     */
	protected  $salesRuleCoupon;

    /**
     * @param \Magento\SalesRule\Helper\Coupon $salesRuleCoupon
     */
    public function __construct(
    	\Magento\SalesRule\Helper\Coupon $salesRuleCoupon
    	) {
    	$this->couponHelper = $salesRuleCoupon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $listOptions = $this->couponHelper->getFormatsList();
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