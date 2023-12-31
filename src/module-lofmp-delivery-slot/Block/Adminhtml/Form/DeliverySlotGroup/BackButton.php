<?php

namespace Lofmp\DeliverySlot\Block\Adminhtml\Form\DeliverySlotGroup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Adminhtml\Form\DeliverySlotGroup\GenericButton;

/**
 * Class BackButton
 * @package Lofmp\DeliverySlot\Form\DeliverySlotGroup
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
        return $this->getUrl('*/*/');
    }
}
