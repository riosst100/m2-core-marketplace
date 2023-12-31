<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
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

namespace Lofmp\TimeDiscount\Block\Adminhtml\Product\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
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
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_systemStore = $systemStore;
        $this->_productloader = $_productloader;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofmptimediscount_product');
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);

        if ($this->_isAllowedAction('Lofmp_TimeDiscount::product')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        //$form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'options_fieldset',
            ['legend' => __('Product Information'), 'class' => 'fieldset-wide fieldset-widget-options']
        );
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }
     
        if(!$model->getProductId()) {
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
            $chooserField->setValue('product/'.$model->getProductId());
            $helperBlock = $this->getLayout()->createBlock(
                'Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser',
                '',
                $helperData
            );

            $helperBlock->setConfig($helperData)
                ->setFieldsetId($fieldset->getId())
                ->prepareElementHtml($chooserField)
                ->setValue($model->getId());
        } else {
             $fieldset->addField(
            'product_id',
            'note',
            [
                'name' => 'product_id',
                'label' => __('Product'),
                'title' => __('Product'),
                'text' => $this->helper->getProductbyId($model->getProductId())->getName()
            ]
            );
        }    
        $fieldset->addField(
            'data',
            'text',
            ['name' => 'data', 'class' => 'requried-entry', 'value' => $model->getData('data')]
        );
        
        $form->getElement(
            'data'
        )->setRenderer(
            $this->getLayout()->createBlock('Lofmp\TimeDiscount\Block\Adminhtml\Product\Renderer\Tab')
        );

      
         if(!$model->getProductId()) {
             // add dependence javascript block
            $dependenceBlock = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence');
            $this->setChild('form_after', $dependenceBlock);
            $dependenceBlock->addFieldMap($chooserField->getId(), 'product_id');
        }
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
        return __('Product Data');
    }
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Product Data');
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
