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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab;

class Extra extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_MarketPlace::seller_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $wysiwygDescriptionConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $model = $this->_coreRegistry->registry('lof_marketplace_seller');

        $fieldset = $form->addFieldset(
            'extra_fieldset',
            ['legend' => __('Extra Fields'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'operating_time',
            'text',
            [
                'name' => 'operating_time',
                'label' => __('Operating Time'),
                'title' => __('Operating Time'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'order_processing_time',
            'text',
            [
                'name' => 'order_processing_time',
                'label' => __('Order Processing Time'),
                'title' => __('Order Processing Time'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'prepare_time',
            'text',
            [
                'name' => 'prepare_time',
                'label' => __('Prepare Time'),
                'title' => __('Prepare Time'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'response_time',
            'text',
            [
                'name' => 'response_time',
                'label' => __('Response Time'),
                'title' => __('Response Time'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'response_ratio',
            'text',
            [
                'name' => 'response_ratio',
                'label' => __('Response Ratio'),
                'title' => __('Response Ratio'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'shipping_partners',
            'editor',
            [
                'name' => 'shipping_partners',
                'style' => 'height:200px;',
                'label' => __('Shipping Partners'),
                'title' => __('Shipping Partners'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        $fieldset->addField(
            'offers',
            'editor',
            [
                'name' => 'offers',
                'style' => 'height:200px;',
                'label' => __('Offers'),
                'title' => __('Offers'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        $fieldset->addField(
            'benefits',
            'editor',
            [
                'name' => 'benefits',
                'style' => 'height:200px;',
                'label' => __('Benefits'),
                'title' => __('Benefits'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        $fieldset->addField(
            'product_shipping_info',
            'editor',
            [
                'name' => 'product_shipping_info',
                'style' => 'height:200px;',
                'label' => __('Product Shipping Info'),
                'title' => __('Product Shipping Info'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Extra Fields');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Extra Fields');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
