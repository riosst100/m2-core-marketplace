<?php

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Coupon;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\CouponCode\Block\MarketPlace\Form\Coupon\GenericButton;

/**
 * Class SaveContinueButton
 * @package Lofmp\CouponCode\Block\MarketPlace\Form\Coupon
 */
class SaveContinueButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'saveAndContinueEdit']],
            ],
            'on_click' => 'return false;',
            'sort_order' => 90,
        ];
    }
}
