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


namespace Lofmp\Rma\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use \Lofmp\Rma\Helper\Data  as DataHelper;
use Lofmp\Rma\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;

class Action extends Form
{
    public function __construct(
        StatusCollectionFactory $statusCollectionFactory,
        DataHelper $dataHelper,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->dataHelper = $dataHelper;
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

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);
        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', [
                'name'  => 'rule_id',
                'value' => $rule->getId(),
            ]);
        }
        $fieldset->addField('status_id', 'select', [
            'label'  => __('Set Status'),
            'name'   => 'status_id',
            'value'  => $rule->getStatusId(),
            'values' => $this->statusCollectionFactory->create()->toOptionArray(true),
        ]);
        $fieldset->addField('user_id', 'select', [
            'label'  => __('Set Owner'),
            'name'   => 'user_id',
            'value'  => $rule->getUserId(),
            'values' => $this->dataHelper->getAdminOptionArray(true),
        ]);

        return parent::_prepareForm();
    }
}
