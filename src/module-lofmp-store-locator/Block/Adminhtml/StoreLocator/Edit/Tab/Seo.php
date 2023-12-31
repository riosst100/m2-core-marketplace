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

class Seo extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
            'seo_url',
            'text',
            [
                'name' => 'seo_url',
                'label' => __('SEO Rewrite Url'),
                'title' => __('SEO Rewrite Url'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'pagetitle',
            'text',
            [
                'name' => 'pagetitle',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'keywords',
            'textarea',
            [
                'name' => 'keywords',
                'label' => __('Meta keywords'),
                'title' => __('Meta keywords'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'meta_description',
            'textarea',
            [
                'name' => 'meta_description',
                'label' => __('Meta Description'),
                'title' => __('Meta Description'),
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
