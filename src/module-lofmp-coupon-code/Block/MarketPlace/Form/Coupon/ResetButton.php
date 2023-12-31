<?php

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Coupon;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\CouponCode\Block\MarketPlace\Form\Coupon\GenericButton;

/**
 * Class ResetButton
 * @package Lofmp\CouponCode\Block\MarketPlace\Form\Coupon
 */
class ResetButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
