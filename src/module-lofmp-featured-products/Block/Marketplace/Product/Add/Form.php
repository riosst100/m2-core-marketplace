<?php

namespace Lofmp\FeaturedProducts\Block\Marketplace\Product\Add;

class Form extends \Lof\MarketPlace\Block\Seller\Widget\Form\Generic
{

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'options_fieldset',
            []
        );

        $chooserField = $fieldset->addField(
            'options_fieldset_entity_id',
            'label',
            [
                'name' => 'product_id',
                'label' => __('Product'),
                'class' => 'widget-option required-entry',
//                'note' => __("Select a product"),
                'required' => true,
            ]
        );
        /*Add chooser helper for the field*/
        $helperData  = [
            'data' => [
                'button' => ['open' => __("Select Product...")]
            ]
        ];
        $helperBlock = $this->getLayout()->createBlock(
            'Lofmp\FeaturedProducts\Block\Marketplace\Product\Chooser',
            '',
            $helperData
        );

        $helperBlock->setConfig($helperData)
            ->setFieldsetId($fieldset->getId())
            ->prepareElementHtml($chooserField);

        $fieldset->addField(
            'featured_from',
            'date',
            [
                'name' => 'featured_from',
                'label' => __('Featured From'),
                'date_format' => $this->_localeDate->getDateFormatWithLongYear(),
            ]
        );

        $fieldset->addField(
            'featured_to',
            'date',
            [
                'name' => 'featured_to',
                'label' => __('Featured To'),
                'date_format' => $this->_localeDate->getDateFormatWithLongYear(),
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'class' => 'validate-number',
            ]
        );

        // add dependence javascript block
//        $dependenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
//        $this->setChild('form_after', $dependenceBlock);
//        $dependenceBlock->addFieldMap($chooserField->getId(), 'product_id');

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
