<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\Config;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Marketplace\Form\Config\GenericButton;

/**
 * Class ResetButton
 * @package Lofmp\DeliverySlot\Block\Marketplace\Form\Config
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
