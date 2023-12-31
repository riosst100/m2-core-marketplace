<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\Config;

use Lofmp\DeliverySlot\Block\Marketplace\Form\Config\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveContinueButton
 * @package Lofmp\DeliverySlot\Block\Marketplace\Form\Config
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
