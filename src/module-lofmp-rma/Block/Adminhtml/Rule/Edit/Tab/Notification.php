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



namespace Lofmp\Rma\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

class Notification extends Form
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
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Lofmp\Rma\Model\Rule $rule */
        $rule = $this->registry->registry('current_rule');

        if ($this->_isAllowedAction('Lofmp_Rma::rma_rma')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $this->_eventManager->dispatch(
            'lof_check_license',
            ['obj' => $this,'ex'=>'Lofmp_Rma']
        );

        if ($this->hasData('is_valid') && $this->hasData('local_valid') && !$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;

        }
        
        $fieldset = $form->addFieldset('notification_fieldset', ['legend' => __('Notifications')]);
        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', [
                'name'  => 'rule_id',
                'value' => $rule->getId(),
            ]);
        }
        $fieldset->addField('is_send_user', 'select', [
            'label'  => __('Send RMA Manager '),
            'name'   => 'is_send_owner',
            'value'  => $rule->getIsSendOwner(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('is_send_customer', 'select', [
            'label'  => __('Send Customer '),
            'name'   => 'is_send_user',
            'value'  => $rule->getIsSendUser(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('other_email', 'text', [
            'label' => __('Send to other email'),
            'name'  => 'other_email',
            'value' => $rule->getOtherEmail(),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('email_subject', 'text', [
            'label' => __('Email subject'),
            'name'  => 'email_subject',
            'value' => $rule->getEmailSubject(),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('email_body', 'textarea', [
            'label' => __('Email body'),
            'name'  => 'email_body',
            'value' => $rule->getEmailBody(),
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('is_send_attachment', 'select', [
            'label'  => __('Attach files'),
            'name'   => 'is_send_attachment',
            'value'  => $rule->getIsSendAttachment(),
            'values' => [0 => __('No'), 1 => __('Yes')],
            'note' => __('is send attachment which were attached to the last message'),
            'disabled' => $isElementDisabled
        ]);

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
