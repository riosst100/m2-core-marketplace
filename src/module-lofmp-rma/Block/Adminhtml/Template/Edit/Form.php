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



namespace Lofmp\Rma\Block\Adminhtml\Template\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use \Magento\Store\Model\System\Store as systemStore;

class Form extends WidgetForm
{
    public function __construct(
        systemStore $systemStore,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        /** @var \Lofmp\Rma\Model\QuickResponse $template */
        $template = $this->registry->registry('current_template');

        $fieldset = $form->addFieldset('edit_fieldset', ['legend' => __('General Information')]);
        if ($template->getId()) {
            $fieldset->addField('template_id', 'hidden', [
                'name'  => 'template_id',
                'value' => $template->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label' => __('Title'),
            'name'  => 'name',
            'value' => $template->getName(),
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'  => __('Is Active'),
            'name'   => 'is_active',
            'value'  => $template->getIsActive(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        $fieldset->addField('allow_seller', 'select', [
            'label'  => __('Is Allow Seller'),
            'name'   => 'allow_seller',
            'value'  => $template->getAllowSeller(),
            'values' => [0 => __('No'), 1 => __('Yes')],
        ]);

        $fieldset->addField('template', 'textarea', [
            'label' => __('Template'),
            'name'  => 'template',
            'value' => $template->getTemplate(),
            'note'  => __(
                'You can use variables:
                [rma_increment_id],                
                [rma_updated_at],
                [customer_email],
                [customer_firstname],
                [customer_lastname],
                [store_name],
                [user_firstname],
                [user_lastname],
                [user_email],
                [seller_name],
                [seller_email]'
            ),
        ]);

        $fieldset->addField('store_ids', 'multiselect', [
            'label'    => __('Stores'),
            'required' => true,
            'name'     => 'store_ids[]',
            'value'    => $template->getStoreIds(),
            'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
