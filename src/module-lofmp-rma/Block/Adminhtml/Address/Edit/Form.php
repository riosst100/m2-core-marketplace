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
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Adminhtml\Address\Edit;

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
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id' => 'edit_form',
            'action' => $this->getUrl(
                '*/*/save',
                [
                    'id' => $this->getRequest()->getParam('id'),
                ]
            ),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        if ($this->_isAllowedAction('Lofmp_Rma::rma_return_addresses')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $this->_eventManager->dispatch(
            'lof_check_license',
            ['obj' => $this, 'ex' => 'Lofmp_Rma']
        );

        if ($this->hasData('is_valid') && $this->hasData('local_valid') && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;

        }

        $address = $this->registry->registry('current_address');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General')]);
        if ($address->getId()) {
            $fieldset->addField('address_id', 'hidden', [
                'name' => 'status_id',
                'value' => $address->getId(),
            ]);
        }
        $fieldset->addField('seller_id', 'text', [
            'label' => __('Seller Id'),
            'name' => 'seller_id',
            'value' => $address->getSellerId(),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('name', 'text', [
            'label' => __('Title'),
            'name' => 'name',
            'value' => $address->getName(),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('address', 'textarea', [
            'label' => __('Return Address'),
            'name' => 'address',
            'value' => $address->getAddress(),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('sort_order', 'text', [
            'label' => __('Sort Order'),
            'name' => 'sort_order',
            'value' => $address->getSortOrder(),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('is_active', 'select', [
            'label' => __('Is Active'),
            'name' => 'is_active',
            'value' => $address->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'disabled' => $isElementDisabled
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
