<?php

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Rule;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\CouponCode\Block\MarketPlace\Form\Rule\GenericButton;

/**
 * Class SaveButton
 * @package Lofmp\CouponCode\Block\MarketPlace\Form\Rule
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'on_click' => 'return false;',
            'sort_order' => 90,
        ];
    }
}
