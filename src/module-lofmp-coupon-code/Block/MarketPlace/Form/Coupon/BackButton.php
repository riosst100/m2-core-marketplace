<?php

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Coupon;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\CouponCode\Block\MarketPlace\Form\Coupon\GenericButton;

/**
 * Class BackButton
 * @package Lofmp\CouponCode\Block\MarketPlace\Form\Coupon
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
    public function getBackUrl()
    {
        return $this->getUrl('lofmpcouponcode/coupon/index/');
    }
}
