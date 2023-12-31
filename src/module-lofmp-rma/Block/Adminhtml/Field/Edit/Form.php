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

namespace Lofmp\Rma\Block\Adminhtml\Field\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Lofmp\Rma\Helper\Help as Helper;
use Lofmp\Rma\Model\Config\Source\Field\Type as FieldTypeSource;
use Lofmp\Rma\Model\Config\Source\Rma\Status;

class Form extends WidgetForm
{
    public function __construct(
        FieldTypeSource $configSourceFieldType,
        Status $status,
        Helper $helper,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->sourceFiledType = $configSourceFieldType;
        $this->status = $status;
        $this->helper = $helper;
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
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

        $field = $this->registry->registry('current_field');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($field->getId()) {
            $fieldset->addField('field_id', 'hidden', [
                'name' => 'field_id',
                'value' => $field->getId(),
            ]);
        }
        $fieldset->addField('store_id', 'hidden', [
            'name' => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store'),
        ]);

        $fieldset->addField('name', 'text', [
            'label' => __('Title'),
            'required' => true,
            'name' => 'name',
            'value' => $field->getName(),
            'scope_label' => __('[STORE VIEW]'),
        ]);
        $fieldset->addField('code', 'text', [
            'label' => __('Code'),
            'required' => true,
            'name' => 'code',
            'value' => $field->getCode(),
            'note' => 'Internal field. Can contain only letters, digits and underscore.',
            'disabled' => $field->getId(),
            '',
            'disabled',
        ]);
        $fieldset->addField('type', 'select', [
            'label' => __('Type'),
            'required' => true,
            'name' => 'type',
            'value' => $field->getType(),
            'values' => $this->sourceFiledType->toOptionArray(),
            'onChange' => 'showHideField()',
        ]);

        $fieldset->addField('description', 'textarea', [
            'label' => __('Description'),
            'name' => 'description',
            'value' => $field->getDescription(),
            'scope_label' => __('[STORE VIEW]'),
        ]);
        $fieldset->addField('values', 'textarea', [
            'label' => __('Options list'),
            'name' => 'values',
            'value' => $this->helper->getStoreViewValue($field, 'values'),
            'note' => __('
                <br>Enter each value from the new line using format:
                <br>value1 | label1
                <br>value2 | label2'),
            'hidden',
            'scope_label' => __('[STORE VIEW]'),
        ]);
        $fieldset->addField('is_active', 'select', [
            'label' => __('Active'),
            'name' => 'is_active',
            'value' => $field->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort order'),
            'name' => 'sort_order',
            'value' => $field->getSortOrder(),
        ]);
        $fieldset->addField('is_required_staff', 'select', [
            'label' => __('Is required for staff'),
            'name' => 'is_required_staff',
            'value' => $field->getIsRequiredStaff(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $fieldset->addField('is_required_customer', 'select', [
            'label' => __('Is required for customers'),
            'name' => 'is_required_customer',
            'value' => $field->IsCustomerRequired(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $statuses = $this->status->toOptionArray();
        array_unshift($statuses, [
            'value' => 'initial',
            'label' => 'RMA Creation',
        ]);
        $fieldset->addField('visible_customer_status', 'multiselect', [
            'label' => __('Visible for customers in statuses'),
            'name' => 'visible_customer_status[]',
            'value' => $field->getVisibleCustomerStatus(),
            'values' => $statuses,
        ]);
        $fieldset->addField('is_show_in_confirm_shipping', 'select', [
            'label' => __('Is show in confirm shipping dialog'),
            'name' => 'is_show_in_confirm_shipping',
            'value' => $field->getIsShowInConfirmShipping(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);
        $fieldset->addField('is_editable_customer', 'select', [
            'label' => __('Is editable for customers'),
            'name' => 'is_editable_customer',
            'value' => $field->getIsEditableCustomer(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        // write this before  this line $this->setForm($form);
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )->addFieldMap(
                "type",
                'type'
            )
                ->addFieldMap(
                    "values",
                    'values'
                )
                ->addFieldDependence(
                    'values',
                    'type',
                    'select'
                )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
