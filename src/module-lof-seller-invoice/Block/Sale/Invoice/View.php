<?php

namespace Lof\SellerInvoice\Block\Sale\Invoice;

class View extends \Lof\MarketPlace\Block\Sale\Invoice\View
{

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
/*    protected function _prepareForm(){

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $fieldset->addField(
            'abc_xyz',
            'text',
            [
                'label' => __('Show In Sidebar'),
                'title' => __('Show In Sidebar'),
                'name' => 'shown_in_sidebar',
            ]
        );

        $this->setForm($form);

        return parent::_prepareForm();
    }*/
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('View Invoice'));
        return parent::_prepareLayout ();
    }
}