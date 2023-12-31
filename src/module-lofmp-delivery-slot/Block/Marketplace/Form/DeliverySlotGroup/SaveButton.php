<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup\GenericButton;

/**
 * Class SaveButton
 * @package Lofmp\DeliverySlot\Form\DeliverySlotGroup
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
