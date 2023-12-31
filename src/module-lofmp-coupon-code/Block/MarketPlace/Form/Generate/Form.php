<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Block\MarketPlace\Form\Generate;

/**
 * Adminhtml report filter form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Report type options
     *
     * @var array
     */
    protected $_reportTypeOptions = [];

    /**
     * Report field visibility
     *
     * @var array
     */
    protected $_fieldVisibility = [];

    /**
     * Report field opions
     *
     * @var array
     */
    protected $_fieldOptions = [];

    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Lofmp\CouponCode\Helper\Data $helper,
        array $data = []
        ) {
        
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_helper = $helper;
    }

    /**
     * Add fieldset with general report fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        // die($this->_helper->getAllRule());
        $actionUrl = $this->getUrl('*/generate/generate');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'generate_form',
                    'action' => $actionUrl,
                    'method' => 'post'
                ]
            ]
        );

        $htmlIdPrefix = 'lofmp_couponcode_generate_';
        $form->setHtmlIdPrefix($htmlIdPrefix);
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Generate Code for Visitor/Customer email')]);

        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        $fieldset->addField('store_ids', 'hidden', ['name' => 'store_ids']);

        $fieldset->addField(
            'email_visitor',
            'text',
            [
                'name' => 'email_visitor',
                'class' => 'validate-email',
                'label' => __('Visitor/Customer Email'),
                'title' => __('Visitor/Customer Email'),
                'note' => __('Required to innput the email address when rule enabled option "Enable check email address when use coupon code".')
            ]
        );
        $fieldset->addField(
            'coupon_rule_id',
            'select',
            [
                'label'    => __('Choose Coupon Rule'),
                'title'    => __('Choose Coupon Rule'),
                'name'     => 'coupon_rule_id',
                'options'  => $this->_helper->getAllRule()
            ]
        ); 
        $fieldset->addField(
            'generate_coupon',
            'submit',
            [
                'label'    => '',
                'title'    => '',
                'class'    => 'action-secondary' ,
                'name'     => 'generate_coupon',
                'checked' => false,
                'onchange' => "",
                'value' => __('Generate Coupon'),
            ]

        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

   
}
