<?php

namespace Lofmp\DeliverySlot\Block\Adminhtml\Form\DeliverySlotGroup;

use Lofmp\DeliverySlot\Block\Adminhtml\Form\DeliverySlotGroup\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveContinueButton
 * @package Lofmp\DeliverySlot\Form\DeliverySlotGroup
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
            'sort_order' => 90,
        ];
    }
}
