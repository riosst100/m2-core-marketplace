<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Adminhtml\Resolution\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Form constructor.
     *
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData(
            [
                'id' => 'edit_form',
                'action' => $this->getUrl(
                    '*/*/save',
                    [
                        'id' => $this->getRequest()->getParam('id'),
                        'store' => (int)$this->getRequest()->getParam('store')
                    ]
                ),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ]
        );

        $resolution = $this->registry->registry('current_resolution');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($resolution->getId()) {
            $fieldset->addField('resolution_id', 'hidden', [
                'name' => 'resolution_id',
                'value' => $resolution->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('name', 'text', [
            'label' => __('Title'),
            'name' => 'name',
            'value' => $resolution->getName(),
            'scope_label' => __('[STORE VIEW]'),
        ]);
        $fieldset->addField('code', 'text', [
            'label' => __('Code'),
            'required' => true,
            'name' => 'code',
            'value' => $resolution->getCode(),
            'disabled' => $resolution->getCode() == '' ? '' : 'disabled',
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort Order'),
            'name' => 'sort_order',
            'value' => $resolution->getSortOrder(),
        ]);
        $fieldset->addField('is_active', 'select', [
            'label' => __('Is Active'),
            'name' => 'is_active',
            'value' => $resolution->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
