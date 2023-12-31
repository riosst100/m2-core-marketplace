<?php

namespace Lof\SellerInvoice\Model\Config\Source\EmailTemplate;

class NewInvoice implements \Magento\Framework\Option\ArrayInterface
{

    public function  __construct(
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig
        ) {
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
    }

    public function toOptionArray()
    {
        $collection = $this->_templatesFactory->create();
        $collection->load();
        $options = $collection->toOptionArray();
        $templateId = 'si_new_invoice';
        $templateLabel = $this->_emailConfig->getTemplateLabel($templateId);
        $templateLabel = __('%1 (Default)', $templateLabel);
        array_unshift($options, ['value' => $templateId, 'label' => $templateLabel]);
        $emailTemplates = [];
        foreach ($options as $k => $v) {
            $emailTemplates[$v['value']] = $v['label'];
        }
        return $emailTemplates;
    }
}
