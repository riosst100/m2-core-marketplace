<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Block\Adminhtml\Product;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

/**
 * Class Import
 * @package Lof\Auction\Block\Adminhtml\Product
 */
class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'product_id';
        $this->_blockGroup = 'Lof_Auction';
        $this->_controller = 'adminhtml_product';

        parent::_construct();

        $this->buttonList->add(
            'save_import',
            [
                'label' => __('Save Import'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getUrl('lofauction/*/saveImport') . '\')'
            ]
        );
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        if ($this->_isAllowedAction('Lof_Auction::product_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Product'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('lofauction_product')->getId()) {
            return __("Edit Message '%1'", $this->escapeHtml($this->_coreRegistry->registry('lofauction_product')->getTitle()));
        } else {
            return __('New Message');
        }
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

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('lofauction/*/saveImport', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveUrl()
    {
        return $this->getUrl('lofauction/*/saveImport', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
        require([
        'jquery',
        'mage/backend/form'
        ], function(){
             jQuery('#save_import').click(function(){

                var actionUrl = jQuery('#edit_form').attr('action');
                actionUrl = actionUrl.replace('save/','saveImport/');
                jQuery('#edit_form').attr('action', actionUrl);
                jQuery('#edit_form').submit();
            });

            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'page_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'page_content');
                }
            };
        });";
        return parent::_prepareLayout();
    }
}
