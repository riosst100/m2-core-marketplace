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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Block\Adminhtml\SellerBadge\Edit\Tab;

class Conditions extends \Lofmp\SellerRule\Block\Adminhtml\Rule\Edit\Tab\Conditions
{

    const FORM_NAME = 'lofmp_sellerbadge_badge_form';

    /**
     * Conditions constructor.
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
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $conditions,
            $rendererFieldset,
            $data
        );
    }

    /**
     * Register
     */
    public function _construct()
    {
        $this->setRegistryKey('lofmp_sellerbadge_badge');
        parent::_construct();
    }

    /**
     * @inheritdoc
     */
    protected function formInit($model)
    {
        $form = $this->_formFactory->create();
        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setFieldSetId(self::RULE_CONDITIONS_FIELDSET_NAMESPACE)->setNewChildUrl(
            $this->getUrl(
                'lofmp_sellerrule/rule/newConditionHtml',
                ['form' => self::RULE_CONDITIONS_FIELDSET_NAMESPACE, 'form_namespace' => self::FORM_NAME]
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
                'title' => __('Conditions'),
                'data-form-part' => self::FORM_NAME
            ]
        )->setRule(
            $model
        )->setRenderer(
            $this->conditions
        );

        $this->setConditionFormName($model->getConditions(), self::RULE_CONDITIONS_FIELDSET_NAMESPACE, self::FORM_NAME);

        return $form;
    }
}
