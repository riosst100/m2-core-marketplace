<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup\GenericButton;

/**
 * Class ResetButton
 * @package Lofmp\DeliverySlot\Form\DeliverySlotGroup
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
