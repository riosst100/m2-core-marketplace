<?php

namespace CoreMarketplace\CategoryImportExport\Block\Adminhtml\CategoryExport\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getFormSubmitUrl(),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getFormSubmitUrl()
    {
        return $this->getUrl("coremarketplace_categoryimportexport/categoryexport/export");
    }
}
