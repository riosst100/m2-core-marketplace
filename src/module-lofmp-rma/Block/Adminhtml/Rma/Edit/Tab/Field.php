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

namespace Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab;

class Field extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Field constructor.
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Lofmp\Rma\Helper\Data $rmaHelper
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Lofmp\Rma\Helper\Data $rmaHelper,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->rmaHelper = $rmaHelper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * General information form
     *
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareForm()
    {
        $form = $this->formFactory->create();

        $this->setForm($form);
        /** @var \Lofmp\Rma\Model\Rule $rule */
        $rma = $this->registry->registry('current_rma');

        $fieldset = $form->addFieldset('customer_fieldset', ['legend' => __('More Information')]);
        $Fieldcollection = $this->rmaHelper->getFields();
        if ($Fieldcollection) {
            foreach ($Fieldcollection as $field) {
                $fieldset->addField(
                    $field->getCode(),
                    $field->getType(),
                    $this->rmaHelper->getInputParams($field, true, $rma)
                );
            }
        }

        return parent::_prepareForm();
    }
}
