<?php

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Coupon;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\CouponCode\Block\MarketPlace\Form\Coupon\GenericButton;

/**
 * Class DeleteButton
 * @package Lofmp\CouponCode\Block\MarketPlace\Form\Coupon
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label' => __('Delete Slot'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/*/delete', ['couponcode_id' => $this->getId()]);
    }
}
