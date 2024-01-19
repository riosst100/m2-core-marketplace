<?php

namespace CoreMarketplace\MarketPlace\Block\Adminhtml\Seller\Edit\Tab;

class Main extends \Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Main
{
    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var $model \Lof\MarketPlace\Model\Seller */
        $model = $this->_coreRegistry->registry('lof_marketplace_seller');
        $formData = ['country_id' => ""];
        $countries = $this->_country->toOptionArray(false, 'US');
        // $regionCollection = $this->_regionFactory->create()->getCollection()->addCountryFilter(
        //     $formData['country_id']
        // );
        //$regions = $regionCollection->toOptionArray();

        if ($this->_isAllowedAction('Lof_MarketPlace::seller_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Seller Information')]);

        if ($model->getId()) {
            $fieldset->addField('seller_id', 'hidden', ['name' => 'seller_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Seller Name'),
                'title' => __('Seller Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        if ($model->getData('url_key')) {
            $fieldset->addField(
                'link_seller',
                'note',
                [
                    'name' => 'link_seller',
                    'label' => __('Link Seller'),
                    'title' => __('Link Seller'),
                    'text' => $model->getUrl()
                ]
            );
        }
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name' => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'note' => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
            ]
        );

        //init taxvat value
        $model->getTaxvat();

        $fieldset->addField(
            'taxvat',
            'text',
            [
                'name' => 'taxvat',
                'label' => __('Tax/VAT Number'),
                'title' => __('Tax/VAT Number'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'contact_number',
            'text',
            [
                'name' => 'telephone',
                'label' => __('Contact Number'),
                'title' => __('Contact Number'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'shop_title',
            'text',
            [
                'name' => 'shop_title',
                'label' => __('Shop Title'),
                'title' => __('Shop Title'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'group_id',
            'select',
            [
                'label' => __('Seller Group'),
                'title' => __('Seller Group'),
                'name' => 'group_id',
                'required' => true,
                'options' => $this->_sellerGroup->toArray(),
                'disabled' => $isElementDisabled
            ]
        );

        if ($model->getCustomerId()) {
            $customer = $fieldset->addField(
                'customer_id',
                'select',
                [
                    'label' => __('Customer'),
                    'title' => __('Customer'),
                    'name' => 'customer_id',
                    'required' => false,
                    'options' => $this->_viewHelper->getCustomerList($model->getCustomerId()),
                    'disabled' => $isElementDisabled
                ]
            );
            $email = $fieldset->addField(
                "email",
                "text",
                [
                    "label" => __("Email Address"),
                    "class" => "required-entry validate-email",
                    "required" => true,
                    "name" => "email"
                ]
            );
        } else {
            $customer = $fieldset->addField(
                'customer_id',
                'select',
                [
                    'label' => __('Customer'),
                    'title' => __('Customer'),
                    'name' => 'customer_id',
                    'required' => false,
                    'options' => $this->_viewHelper->getCustomerList(),
                    'disabled' => $isElementDisabled
                ]
            );
            $email = $fieldset->addField(
                "email",
                "text",
                [
                    "label" => __("Email Address"),
                    "class" => "required-entry validate-email",
                    "required" => true,
                    "name" => "email"
                ]
            );
        }

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Company Banner'),
                'title' => __('Company Banner'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'thumbnail',
            'image',
            [
                'name' => 'thumbnail',
                'label' => __('Company Logo'),
                'title' => __('Company Logo'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'company',
            'text',
            [
                'name' => 'company',
                'label' => __('Company'),
                'title' => __('Company'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'company_url',
            'text',
            [
                'name' => 'company_url',
                'label' => __('Company URL'),
                'title' => __('Company URL'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'company_locality',
            'text',
            [
                'name' => 'company_locality',
                'label' => __('Company Locality'),
                'title' => __('Company Locality'),
                'disabled' => $isElementDisabled
            ]
        );

        $wysiwygDescriptionConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField(
            'company_description',
            'editor',
            [
                'name' => 'company_description',
                'style' => 'height:200px;',
                'label' => __('Company Description'),
                'title' => __('Company Description'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        $fieldset->addField(
            'return_policy',
            'editor',
            [
                'name' => 'return_policy',
                'style' => 'height:200px;',
                'label' => __('Return Policy'),
                'title' => __('Return Policy'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        $fieldset->addField(
            'shipping_policy',
            'editor',
            [
                'name' => 'shipping_policy',
                'style' => 'height:200px;',
                'label' => __('Shipping Policy'),
                'title' => __('Shipping Policy'),
                'disabled' => $isElementDisabled,
                'config' => $wysiwygDescriptionConfig
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );

            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'telephone',
            'text',
            [
                'name' => 'telephone',
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => true,
                'values' => $countries,
                'disabled' => $isElementDisabled
            ]
        );

        $customregion = $fieldset->addField(
            'region_id',
            'note',
            [
                'label' => __('Region ID'),
                'required' => false,
                'name' => 'region_id',
                'disabled' => $isElementDisabled
            ]
        );

        $customregion->setRenderer($this->getLayout()->createBlock('Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Renderer\Region'));


        // $fieldset->addField(
        //     'region_id',
        //     'select',
        //     [
        //         'name' => 'region_id',
        //         'label' => __('State'),
        //         'title' => __('State'),
        //         'values' => $regions,
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        $fieldset->addField(
            'region',
            'text',
            [
                'name' => 'region',
                'label' => __('Region'),
                'title' => __('Region'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class)
                ->setTemplate('Lof_MarketPlace::country/js.phtml')
        );
        $form->setValues($model->getData());
        $this->setForm($form);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
                ->addFieldMap($customer->getHtmlId(), $customer->getName())
                ->addFieldMap($email->getHtmlId(), $email->getName())
                ->addFieldDependence($email->getName(), $customer->getName(), '0')
        );

        return parent::_prepareForm();
    }
}