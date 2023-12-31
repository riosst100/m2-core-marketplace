<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup\GenericButton;

/**
 * Class DeleteButton
 * @package Lofmp\DeliverySlot\Form\DeliverySlotGroup
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
        return $this->getUrl('*/*/*/delete', ['group_id' => $this->getId()]);
    }
}
