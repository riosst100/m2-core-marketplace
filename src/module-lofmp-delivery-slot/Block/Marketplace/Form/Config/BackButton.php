<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\Config;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Marketplace\Form\Config\GenericButton;

/**
 * Class BackButton
 * @package Lofmp\DeliverySlot\Block\Marketplace\Form\Config
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
        return $this->getUrl('*/*/*/');
    }
}
