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
 * @category  Landofcoder
 * @package   Lof_Setup
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Setup\Block\Adminhtml\Export\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $_yesno;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Lof\Setup\Model\System\Config\Source\Export\LofModules
     */
    protected $_lofModules;

    /**
     * @var \Lof\Setup\Model\System\Config\Source\Export\ExportFolders
     */
    protected $_exportFolders;

    /**
     * @var \Lof\Setup\Model\System\Config\Source\Export\Cmspage
     */
    protected $_cmsPage;

    /**
     * @var \Lof\Setup\Model\System\Config\Source\Export\StaticBlock
     */
    protected $_staticBlock;

    /**
     * @var \Lof\Setup\Model\System\Config\Source\Export\Widgets
     */    
    protected $_widgets;

    /**
     * @var \Lof\Setup\Model\System\Config\Source\Export\FileExtension
     */
    protected $_fileExtension;

    /**
     * @param \Magento\Backend\Block\Template\Context                    $context       
     * @param \Magento\Framework\Registry                                $registry      
     * @param \Magento\Framework\Data\FormFactory                        $formFactory   
     * @param \Magento\Config\Model\Config\Source\Yesno                  $yesno         
     * @param \Lof\Setup\Model\System\Config\Source\Export\ExportFolders $exportFolders 
     * @param \Lof\Setup\Model\System\Config\Source\Export\LofModules    $lofModules    
     * @param \Lof\Setup\Model\System\Config\Source\Export\Cmspage       $cmsPage       
     * @param \Lof\Setup\Model\System\Config\Source\Export\StaticBlock   $staticBlock   
     * @param \Lof\Setup\Model\System\Config\Source\Export\Widgets       $widgets       
     * @param \Magento\Store\Model\System\Store                          $systemStore   
     * @param \Lof\Setup\Model\System\Config\Source\Export\FileExtension $fileExtension 
     * @param array                                                      $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesno,
        \Lof\Setup\Model\System\Config\Source\Export\ExportFolders $exportFolders,
        \Lof\Setup\Model\System\Config\Source\Export\LofModules $lofModules,
        \Lof\Setup\Model\System\Config\Source\Export\Cmspage $cmsPage,
        \Lof\Setup\Model\System\Config\Source\Export\Staticblock $staticBlock,
        \Lof\Setup\Model\System\Config\Source\Export\Widgets $widgets,
        \Magento\Store\Model\System\Store $systemStore,
        \Lof\Setup\Model\System\Config\Source\Export\FileExtension $fileExtension,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_yesno = $yesno;
        $this->_exportFolders = $exportFolders;
        $this->_lofModules = $lofModules;
        $this->_cmsPage = $cmsPage;
        $this->_staticBlock = $staticBlock;
        $this->_widgets = $widgets;
        $this->_systemStore = $systemStore;
        $this->_fileExtension = $fileExtension;
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /**
         * Checking if user have permission to save information
         */
        if($this->_isAllowedAction('Lof_Setup::export')) {
            $isElementDisabled = false;
        }else {
            $isElementDisabled = true;
        }

        /**
 * @var \Magento\Framework\Data\Form $form 
*/
        $form = $this->_formFactory->create(
            [
                    'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                    ]
                ]
        );

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Lof Setup Export')]);
        $fieldset->addField(
            'file_name',
            'text',
            [
                    'name' => 'file_name',
                    'label' => __('File Name'),
                    'title' => __('File Name'),
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'note' => __('This will be the name of the file in which configuration will be saved. You can enter any name you want.')
                ]
        );

        $fieldset->addField(
            'file_extension',
            'select',
            [
                    'name' => 'file_extension',
                    'label' => __('File Extension'),
                    'title' => __('File Extension'),
                    'options' => $this->_fileExtension->toArray(),
                    'disabled' => $isElementDisabled
                ]
        );

        $field = $fieldset->addField(
            'store_id',
            'select',
            [
                    'name' => 'store_id',
                    'label' => __('Configuration Scope'),
                    'title' => __('Configuration Scope'),
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
                    'note' => __('Configuration of selected store will be saved in a file. Apply for all system config of modules')
                ]
        );

        $field = $fieldset->addField(
            'modules',
            'multiselect',
            [
                    'name' => 'modules[]',
                    'label' => __('Lof Modules'),
                    'title' => __('Lof Modules'),
                    'values' => $this->_lofModules->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
        );

        $renderer = $this->getLayout()->createBlock(
            'Lof\Setup\Block\Adminhtml\Form\Renderer\Fieldset\Element'
        );
        $field->setRenderer($renderer);

        $field = $fieldset->addField(
            'cmspages',
            'multiselect',
            [
                    'name' => 'cmspages[]',
                    'label' => __('CMS Pages'),
                    'title' => __('CMS Pages'),
                    'values' => $this->_cmsPage->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
        );

        $renderer = $this->getLayout()->createBlock(
            'Lof\Setup\Block\Adminhtml\Form\Renderer\Fieldset\Element'
        );
        $field->setRenderer($renderer);

        $field = $fieldset->addField(
            'cmsblocks',
            'multiselect',
            [
                    'name' => 'cmsblocks[]',
                    'label' => __('CMS Blocks'),
                    'title' => __('CMS Blocks'),
                    'values' => $this->_staticBlock->toOptionArray(),
                    'disabled' => $isElementDisabled
                ]
        );

        $renderer = $this->getLayout()->createBlock(
            'Lof\Setup\Block\Adminhtml\Form\Renderer\Fieldset\Element'
        );
        $field->setRenderer($renderer);

        $field = $fieldset->addField(
            'widgets',
            'multiselect',
            [
                'name' => 'widgets[]',
                'label' => __('Widgets'),
                'title' => __('Widgets'),
                'values' => $this->_widgets->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Lof\Setup\Block\Adminhtml\Form\Renderer\Fieldset\Element'
        );
        $field->setRenderer($renderer);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Check permission for passed action
     *
     * @param  string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}