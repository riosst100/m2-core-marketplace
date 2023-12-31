<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab;

class Gmap extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;
    protected $_wysiwygConfig;
    protected $_storelocatorCollection;
    protected $_drawLevel;
    protected $_countryFactory;
    protected $_regionFactory;
    protected $_tagCollection;
    protected $_categoryCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lofmp\StoreLocator\Model\ResourceModel\StoreLocator\CollectionFactory $storelocatorCollection,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Magento\Directory\Model\Config\Source\Allregion $regionFactory,
        \Lofmp\StoreLocator\Model\ResourceModel\Tag\CollectionFactory $tagCollection,
        \Lofmp\StoreLocator\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_storelocatorCollection = $storelocatorCollection;
        $this->_countryFactory = $countryFactory;
        $this->_regionFactory = $regionFactory;
        $this->_tagCollection = $tagCollection;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $this->_eventManager->dispatch(
        'lof_check_license',
        ['obj' => $this,'ex'=>'Lofmp_StoreLocator']
        );
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('storelocator_storelocator');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lofmp_StoreLocator::storelocator_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
       
       if (!$this->getData('is_valid') && !$this->getData('local_valid')) {
            $isElementDisabled = true;
            $wysiwygConfig['enabled'] = $wysiwygConfig['add_variables'] = $wysiwygConfig['add_widgets'] = $wysiwygConfig['add_images'] = 0;
            $wysiwygConfig['plugins'] = [];

        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('storelocator_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('StoreLocator Information')]);


        $_tagValue = array();
        $_categoryValue = array();

        if ($model->getId()) {
            $fieldset->addField('storelocator_id', 'hidden', ['name' => 'storelocator_id']);

            $tag = $model->getTag();
            
            if (!is_array($tag)){
                $_tagValue = explode(",", $tag);

            }

            $category = $model->getCategory();
            if (!is_array($category)){
                $_categoryValue = explode(",", $category);

            }
        }

        
        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'zipcode',
            'text',
            [
                'name' => 'zipcode',
                'label' => __('Zip Code'),
                'title' => __('Zip Code'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );


        $optionsc = $this->_countryFactory->toOptionArray();

        //echo "<pre>"; print_r($optionsc); die;

        $country = $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $optionsc,
            ]
        );

        

        $fieldset->addField(
            'telephone',
            'text',
            [
                'name' => 'telephone',
                'label' => __('Telephone'),
                'title' => __('Telephone'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'website',
            'text',
            [
                'name' => 'website',
                'label' => __('website'),
                'title' => __('website'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );


        // $model->setData('zoomlevel', '18');
        // $fieldset->addField(
        //     'zoomlevel',
        //     'text',
        //     [
        //         'name' => 'zoomlevel',
        //         'label' => __('Zoom Level'),
        //         'title' => __('Zoom Level'),
        //         'required' => true,
        //         'disabled' => true
        //     ]
        // );

        // $fieldset->addField('Tag', 'checkboxes', array(
        //     'label' => __('Tag'),
        //     'name' => 'tag[]',
        //     'value' => '2',
        //     'values' => $this->getTagCollection(),
        //     'after_element_html' => '<br><small>Choose tag that you want filter storelocator</small>',
        //     'checked' => $_tagValue,
        // ));

        // $fieldset->addField('Category', 'checkboxes', array(
        //     'label' => __('Category'),
        //     'name' => 'category[]',
        //     'value' => '2',
        //     'values' => $this->getCategoryCollection(),
        //     'after_element_html' => '<br><small>Choose Category that you want filter storelocator</small>',
        //     'checked' => $_categoryValue,
        // ));



        $fieldset->addField(
            'color',
            'text',
            [
                'name' => 'color',
                'label' => __('Marker Color'),
                'title' => __('Marker Color'),
                'class' => 'minicolors',
                'style' => 'padding-left:36px;',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'fontClass',
            'text',
            [
                'name' => 'fontClass',
                'label' => __('Marker FontIcon'),
                'title' => __('Marker FontIcon'),
                'after_element_html' => '<br><small>Get fontClass at website <a href="http://fontawesome.io/icons/">http://fontawesome.io</a><br>Example: fa-apple, fa-bitbucket, fa-bluetooth, fa-cc-visa</small>',
                'required' => true,
                'value' => 'fa fa-map-marker'
            ]
        );

        $fieldset->addField(
            'address',
            'text',
            [
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addType('gmap','Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Form\Field\Gmap');
        $fieldset->addField(
            'gmap',
            'gmap',
            [
                'name' => 'gmap',
                'label' => __(null),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'lat',
            'text',
            [
                'name' => 'lat',
                'label' => __('lat'),
                'title' => __('lat'),
                'required' => false,
                'readonly' => true,
            ]
        );
        $fieldset->addField(
            'lng',
            'text',
            [
                'name' => 'lng',
                'label' => __('lng'),
                'title' => __('lng'),
                'required' => false,
                'readonly' => true,
            ]
        );
        $fieldset->addField(
            'address2',
            'text',
            [
                'name' => 'address2',
                'label' => __('Address2'),
                'title' => __('Address2'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_storelocator_storelocator_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }


    public function getCategoryCollection(){

        $_data = $this->_categoryCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
             $_resultData[]    =   array(
                'value'        =>  $result['name'],
                'label'        =>  $result['name'],
            );
        }
        return $_resultData;
    }

    public function getTagCollection(){

        $_data = $this->_tagCollection->create();
        $_locationData = $_data->getData();
        $_resultData = array();
        foreach ($_locationData as $result) {
             $_resultData[]    =   array(
                'value'        =>  $result['name'],
                'label'        =>  $result['name'],
            );
        }
        return $_resultData;
    }


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('StoreLocator Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('StoreLocator Information');
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
