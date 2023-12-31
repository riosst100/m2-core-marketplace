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

namespace Lofmp\Rma\Block\Adminhtml\Condition\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Form extends WidgetForm
{
    public function __construct(
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->formFactory->create()->setData([
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
        ]);

        /** @var \Lofmp\Rma\Model\Condition $condition */
        $condition = $this->registry->registry('current_condition');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General')]);
        if ($condition->getId()) {
            $fieldset->addField('condition_id', 'hidden', [
                'name' => 'condition_id',
                'value' => $condition->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('name', 'text', [
            'label' => __('Title'),
            'name' => 'name',
            'value' => $condition->getName(),
            'scope_label' => __('[STORE VIEW]'),
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort Order'),
            'name' => 'sort_order',
            'value' => $condition->getSortOrder(),
        ]);
        $fieldset->addField('is_active', 'select', [
            'label' => __('Is Active'),
            'name' => 'is_active',
            'value' => $condition->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
