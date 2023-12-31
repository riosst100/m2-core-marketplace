<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Block\Seller\Product\Edit;

class Form extends \Lof\MarketPlace\Block\Seller\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
     /**
     * @var \Lofmp\TimeDiscount\Model\Config\Source\MethodTypes 
     */
    protected $methodTypes;

    protected $_productloader;
        /**
     * @var array configuration of TimeDiscount
     */

    protected $helper;


    protected $marketHelper;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Lofmp\TimeDiscount\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        array $data = []
    ) {
        $this->marketHelper = $marketHelper;
        $this->helper = $helper;
        $this->_systemStore = $systemStore;
        $this->_productloader = $_productloader;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'options_fieldset',
            ['legend' => __('TimeDiscount Product Information'), 'class' => 'fieldset-wide fieldset-widget-options']
        );
        if(!$this->getProduct()->getProductId()) {
            $chooserField = $fieldset->addField(
                'options_fieldset_entity_id',
                'label',
                [
                    'name' => 'product_id',
                    'label' => __('Product'),
                    'required' => true,
                    'class' => 'widget-option',
                    'note' => __("Select a product"),
                ]
            );
            /*Add chooser helper for the field*/
            $helperData  = [
                'data' => [
                    'button' => ['open' => __("Select Product...")]
                ]
            ];
            $chooserField->setValue($this->getProduct()->getProductId());
            $helperBlock = $this->getLayout()->createBlock(
                'Lofmp\TimeDiscount\Block\Seller\Product\Chooser',
                '',
                $helperData
            );

            $helperBlock->setConfig($helperData)
                ->setFieldsetId($fieldset->getId())
                ->prepareElementHtml($chooserField)
                ->setValue($this->getProduct()->getId());
        } else {
             $fieldset->addField(
            'product_id',
            'note',
            [
                'name' => 'product_id',
                'label' => __('Product'),
                'title' => __('Product'),
                'text' => $this->helper->getProductbyId($this->getProduct()->getProductId())->getName()
            ]
            );
           
        }
       
         $fieldset->addField(
            'data',
            'text',
            ['name' => 'data', 'class' => 'requried-entry', 'value' => $this->getProduct()->getData('data')]
        );
        
        $form->getElement(
            'data'
        )->setRenderer(
            $this->getLayout()->createBlock('Lofmp\TimeDiscount\Block\Adminhtml\Product\Renderer\Tab')
        );
        // add dependence javascript block
        $dependenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
        $this->setChild('form_after', $dependenceBlock);
        if(!$this->getProduct()->getProductId()) {
            $dependenceBlock->addFieldMap($chooserField->getId(), 'product_id');
        }
        
        $data = $this->getProduct()->getData();
        /* $data['options_fieldset_product_id'] = $this->getProduct()->getName(); */
        $form->setValues($data);
        
        
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    /**
     * Get current featured product
     * 
     * @return \Lofmp\TimeDiscount\Model\Product|null
     */
    public function getProduct(){
        return $this->_coreRegistry->registry('current_product');
    }
}
