<?php

namespace Lofmp\DeliverySlot\Block\Adminhtml\Form\DeliverySlotGroup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Adminhtml\Form\DeliverySlotGroup\GenericButton;

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
            'sort_order' => 90,
        ];
    }
}
