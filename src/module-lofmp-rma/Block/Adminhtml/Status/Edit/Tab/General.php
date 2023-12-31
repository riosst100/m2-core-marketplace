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



namespace Lofmp\Rma\Block\Adminhtml\Status\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class General extends Form
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
        $storeId = (int)$this->getRequest()->getParam('store');
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lofmp\Rma\Model\Rule $rule */
        $status = $this->registry->registry('current_status');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($status->getId()) {
            $fieldset->addField('status_id', 'hidden', [
                'name'  => 'status_id',
                'value' => $status->getId(),
            ]);
        }

        $fieldset->addField('store_id', 'hidden', [
            'name'  => 'store_id',
            'value' => $storeId,
        ]);

        $fieldset->addField('name', 'text', [
            'label'       => __('Title'),
            'name'        => 'name',
            'value'       => $status->getName(),
            'required'    => true,
            'scope_label' => __('[STORE VIEW]'),
        ]);

        $fieldset->addField('code', 'text', [
            'label'    => __('Code'),
            'name'     => 'code',
            'value'    => $status->getCode(),
            'disabled' => $status->getCode() == '' ? '' : 'disabled',
            'required' => true,
        ]);

        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort Order'),
            'name'  => 'sort_order',
            'value' => $status->getSortOrder(),
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'  => __('Is Active'),
            'name'   => 'is_active',
            'value'  => $status->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        $fieldset->addField('is_show_shipping', 'select', [
            'label'  => __('Show shipping'),
            'name'   => 'is_show_shipping',
            'value'  => $status->getIsShowShipping(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'note'    => __('Show shipping buttons \'Print RMA Packing Slip\','.
                ' \'Print RMA Shipping Label\' and \'Confirm Shipping\' in the customer account'),
        ]);

        return parent::_prepareForm();
    }
}
