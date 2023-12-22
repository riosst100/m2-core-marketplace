<?php
/**
 * Webcastle_CategoryImportExport
 *
 * @category   Webcastle
 * @package    Webcastle_CategoryImportExport
 * @author     Anjaly K V - Webcastle Media
 * @copyright  2023
 */
namespace Webcastle\CategoryImportExport\Block\Adminhtml\CategoryImport\Edit\Tab;

class CategoryImport extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storemanager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $url = $this->getViewFileUrl('Webcastle_CategoryImportExport/webcastle_importcategory.zip');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Import Categories'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'file',
            'file',
            [
                'name'  => 'file',
                'label' => __('Upload File'),
                'title' => __('Upload File'),
                'required' => true,
            ]
        );
              
        $fieldset = $form->addFieldset(
            'sample_files',
            [
                'legend' => __('Sample Files'),
                'class'  => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'import_sample',
            'button',
            [
                'name'  => 'import_sample',
                'label' => __('Sample Files'),
                'text' => __('Sample Files'),
                'value'     => __('Sample Files'),
                'onclick'   => "javascript:window.location = '$url' ",
            ]
        ); 

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Import Categories');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
