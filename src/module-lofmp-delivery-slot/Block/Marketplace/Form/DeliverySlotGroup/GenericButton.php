<?php

namespace Lofmp\DeliverySlot\Block\Marketplace\Form\DeliverySlotGroup;

use Magento\Backend\Block\Widget\Context;;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry
    ) {
    
        $this->context = $context;
        $this->registry = $registry;
    }
    public function getId()
    {
        $group = $this->registry->registry('group_id');
        return $group ? $group : null;
    }
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
