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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Block\Marketplace\Shipping;

class Upload extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
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
        $this->_objectId = 'shipping_id';
        $this->_blockGroup = 'Lofmp_TableRateShipping';
        $this->_controller = 'marketplace_shipping';

        parent::_construct();

        $this->buttonList->add(
            'save_upload',
            [
                'label' => __('Save Upload'),
                'class' => 'save primary',
                'onclick' => 'setLocation(\'' . $this->getUrl('lofmptablerateshipping/*/save_upload') . '\')'
            ]
        );
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        if ($this->_isAllowedAction('Lofmp_TableRateShipping::shipping_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Shipping'));
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
        if ($this->_coreRegistry->registry('lofmptablerateshipping_shipping')->getId()) {
            return __(
                "Edit Message '%1'",
                $this->escapeHtml($this->_coreRegistry->registry('lofmptablerateshipping_shipping')->getTitle())
            );
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
        return $this->getUrl(
            'lofmptablerateshipping/*/save',
            ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']
        );
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
             jQuery('#save_upload').click(function(){

                var actionUrl = jQuery('#edit_form').attr('action') + 'save_upload/1';
                 console.log(actionUrl);
                jQuery('#edit_form').attr('action', actionUrl);
                jQuery('#edit_form').submit();
                actionUrl = actionUrl.replace('save_upload/1','');
                jQuery('#edit_form').attr('action', actionUrl);
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
