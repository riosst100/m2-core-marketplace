<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Rule\Model\Condition\AbstractCondition;

class Conditions extends \Lofmp\SellerRule\Block\Adminhtml\Rule\Edit\Tab\AbstractTab
{
    const RULE_CONDITIONS_FIELDSET_NAMESPACE = 'rule_conditions_fieldset';

    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Conditions constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        array $data = []
    ) {
        $this->conditions = $conditions;
        $this->rendererFieldset = $rendererFieldset;
        $this->_formFactory = $formFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        $form = $this->formInit($model);
        $form->setValues($model->getData());
        $form->addValues(['id' => $model->getId()]);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    protected function getModel()
    {
        return $this->_coreRegistry->registry($this->registryKey);
    }

    /**
     * @inheritdoc
     */
    protected function formInit($model)
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl(
                'lofmp_sellerrule/rule/newConditionHtml',
                ['form' => self::RULE_CONDITIONS_FIELDSET_NAMESPACE]
            )
        );

        $fieldset = $form->addFieldset(
            self::RULE_CONDITIONS_FIELDSET_NAMESPACE,
            [
                'legend' => __(
                    'Conditions (don\'t add conditions if rule is applied to all sellers)'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            [
                'name' => 'conditions',
                'label' => __('Conditions'),
                'title' => __('Conditions')
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->conditions
        );

        $this->setConditionFormName($model->getConditions(), self::RULE_CONDITIONS_FIELDSET_NAMESPACE);

        return $form;
    }

    /**
     * @param AbstractCondition $conditions
     * @param $fieldsetName
     * @param null $formName
     */
    protected function setConditionFormName(
        \Magento\Rule\Model\Condition\AbstractCondition $conditions,
        $fieldsetName,
        $formName = null
    ) {
        if ($formName) {
            $conditions->setFormName($formName);
        }
        $conditions->setJsFormObject($fieldsetName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $fieldsetName, $formName);
            }
        }
    }
}
