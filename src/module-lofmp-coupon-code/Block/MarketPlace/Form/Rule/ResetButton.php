<?php

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Rule;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\CouponCode\Block\MarketPlace\Form\Rule\GenericButton;

/**
 * Class ResetButton
 * @package Lofmp\CouponCode\Block\MarketPlace\Form\Rule
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
