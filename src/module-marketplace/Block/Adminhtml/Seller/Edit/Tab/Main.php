<?php

namespace CoreMarketplace\MarketPlace\Block\Adminhtml\Seller\Edit\Tab;

class Main extends \Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Main
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_viewHelper;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $_country;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * @var \Lof\MarketPlace\Model\Config\Source\SellerGroup
     */
    protected $_sellerGroup;

    /**
     * @var \CoreMarketplace\MarketPlace\Model\Config\Source\SellerType
     */
    protected $_sellerType;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country\Full
     */
    protected $countryFull;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Lof\MarketPlace\Helper\Data $viewHelper
     * @param \Magento\Directory\Model\Config\Source\Country $country
     * @param \Magento\Directory\Model\Config\Source\Country\Full $countryFull
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Lof\MarketPlace\Model\Config\Source\SellerGroup $sellerGroup
     * @param \CoreMarketplace\MarketPlace\Model\Config\Source\SellerType $sellerType
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\MarketPlace\Helper\Data $viewHelper,
        \Magento\Directory\Model\Config\Source\Country $country,
        \Magento\Directory\Model\Config\Source\Country\Full $countryFull,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Lof\MarketPlace\Model\Config\Source\SellerGroup $sellerGroup,
        \CoreMarketplace\MarketPlace\Model\Config\Source\SellerType $sellerType,
        array $data = []
    ) {
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_country = $country;
        $this->_countryFull = $countryFull;
        $this->_regionFactory = $regionFactory;
        $this->_sellerGroup = $sellerGroup;
        $this->_sellerType = $sellerType;
        parent::__construct(
            $context, 
            $registry, 
            $formFactory, 
            $systemStore,
            $wysiwygConfig,
            $viewHelper,
            $country,
            $regionFactory,
            $sellerGroup,
            $data
        );
    }

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
        $shipToCountries = $this->_countryFull->toOptionArray(true, null);
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

        $htmlIdPrefix = 'seller_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Seller Information')]);

        if ($model->getId()) {
            $fieldset->addField('seller_id', 'hidden', ['name' => 'seller_id']);
        }

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

        $fieldset->addField(
            'seller_type',
            'select',
            [
                'label' => __('Seller Type'),
                'title' => __('Seller Type'),
                'name' => 'seller_type',
                'required' => true,
                'options' => $this->_sellerType->toArray()
            ]
        );

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
            'company_registration_number',
            'text',
            [
                'name' => 'company_registration_number',
                'label' => __('Company Registration Number'),
                'title' => __('Company Registration Number'),
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
        // $model->getTaxvat();

        // $fieldset->addField(
        //     'taxvat',
        //     'text',
        //     [
        //         'name' => 'taxvat',
        //         'label' => __('Tax/VAT Number'),
        //         'title' => __('Tax/VAT Number'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Store Description'),
                'title' => __('Store Description'),
                'required' => false,
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
            'website_url',
            'text',
            [
                'name' => 'website_url',
                'label' => __('Website URL'),
                'title' => __('Website URL'),
                'disabled' => $isElementDisabled
            ]
        );

        $field = $fieldset->addField(
            'ship_to',
            'multiselect',
            [
                'name' => 'ship_to[]',
                'label' => __('Ship To'),
                'title' => __('Ship To'),
                'required' => true,
                'values' => [
                    [
                        'value' => 'domestic',
                        'label' => __('Domestic')
                    ],
                    [
                        'value' => 'international',
                        'label' => __('International')
                    ]
                ],
                'disabled' => $isElementDisabled
            ]
        );

        $field = $fieldset->addField(
            'ship_to_country',
            'multiselect',
            [
                'name' => 'ship_to_country[]',
                'label' => __('Ship To Country'),
                'title' => __('Ship To Country'),
                'required' => true,
                'values' => $shipToCountries,
                'disabled' => $isElementDisabled
            ]
        );

        // $fieldset->addField(
        //     'image',
        //     'image',
        //     [
        //         'name' => 'image',
        //         'label' => __('Company Banner'),
        //         'title' => __('Company Banner'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        // $fieldset->addField(
        //     'thumbnail',
        //     'image',
        //     [
        //         'name' => 'thumbnail',
        //         'label' => __('Company Logo'),
        //         'title' => __('Company Logo'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        // $fieldset->addField(
        //     'company_url',
        //     'text',
        //     [
        //         'name' => 'company_url',
        //         'label' => __('Company URL'),
        //         'title' => __('Company URL'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        // $fieldset->addField(
        //     'company_locality',
        //     'text',
        //     [
        //         'name' => 'company_locality',
        //         'label' => __('Company Locality'),
        //         'title' => __('Company Locality'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        

        /**
         * Check is single store mode
         */
        // if (!$this->_storeManager->isSingleStoreMode()) {
        //     $field = $fieldset->addField(
        //         'store_id',
        //         'multiselect',
        //         [
        //             'name' => 'stores[]',
        //             'label' => __('Store View'),
        //             'title' => __('Store View'),
        //             'required' => true,
        //             'values' => $this->_systemStore->getStoreValuesForForm(false, true),
        //             'disabled' => $isElementDisabled
        //         ]
        //     );

        //     $renderer = $this->getLayout()->createBlock(
        //         \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
        //     );
        //     $field->setRenderer($renderer);
        // } else {
        //     $fieldset->addField(
        //         'store_id',
        //         'hidden',
        //         ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
        //     );
        //     $model->setStoreId($this->_storeManager->getStore(true)->getId());
        // }

        // $fieldset->addField(
        //     'telephone',
        //     'text',
        //     [
        //         'name' => 'telephone',
        //         'label' => __('Phone Number'),
        //         'title' => __('Phone Number'),
        //         'required' => true,
        //         'disabled' => $isElementDisabled
        //     ]
        // );

        $fieldset->addField(
            'address_line_1',
            'text',
            [
                'name' => 'address_line_1',
                'label' => __('Address Line 1'),
                'title' => __('Address Line 1'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'address_line_2',
            'text',
            [
                'name' => 'address_line_2',
                'label' => __('Address Line 2'),
                'title' => __('Address Line 2'),
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
                'label' => __('State / Provice ID'),
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
                'label' => __('State / Provice'),
                'title' => __('State / Provice'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postal Code'),
                'title' => __('Postal Code'),
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

        // $fieldset->addField(
        //     'position',
        //     'text',
        //     [
        //         'name' => 'position',
        //         'label' => __('Position'),
        //         'title' => __('Position'),
        //         'disabled' => $isElementDisabled
        //     ]
        // );
        
        $wysiwygDescriptionConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        // $fieldset->addField(
        //     'company_description',
        //     'editor',
        //     [
        //         'name' => 'company_description',
        //         'style' => 'height:200px;',
        //         'label' => __('Company Description'),
        //         'title' => __('Company Description'),
        //         'disabled' => $isElementDisabled,
        //         'config' => $wysiwygDescriptionConfig
        //     ]
        // );

        $fieldset->addField(
            'term_and_conditions',
            'textarea',
            [
                'name' => 'term_and_conditions',
                'label' => __('Term & Conditions'),
                'title' => __('Term & Conditions'),
                'disabled' => $isElementDisabled
            ]
        );

        // $fieldset->addField(
        //     'shipping_policy',
        //     'editor',
        //     [
        //         'name' => 'shipping_policy',
        //         'style' => 'height:200px;',
        //         'label' => __('Shipping Policy'),
        //         'title' => __('Shipping Policy'),
        //         'disabled' => $isElementDisabled,
        //         'config' => $wysiwygDescriptionConfig
        //     ]
        // );

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

        $this->setChild(
            'form_after', 
            $this->getLayout()->createBlock(\Magento\Backend\Block\Widget\Form\Element\Dependence::class)
            ->addFieldMap("{$htmlIdPrefix}seller_type", 'seller_type')
            ->addFieldMap("{$htmlIdPrefix}company", 'company')
            ->addFieldMap("{$htmlIdPrefix}company_registration_number", 'company_registration_number')
            ->addFieldDependence('company', 'seller_type', 'company')
            ->addFieldDependence('company_registration_number', 'seller_type', 'company')
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class)
                ->setTemplate('CoreMarketplace_MarketPlace::seller/ship-to/js.phtml')
        );

        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Seller Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Seller Information');
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
