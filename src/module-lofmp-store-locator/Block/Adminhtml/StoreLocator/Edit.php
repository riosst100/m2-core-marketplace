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

namespace Lofmp\StoreLocator\Block\Adminhtml\StoreLocator;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context
     * @param \Magento\Framework\Registry
     * @param array
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
        $this->_objectId = 'storelocator_id';
        $this->_blockGroup = 'Lofmp_StoreLocator';
        $this->_controller = 'adminhtml_storeLocator';

        parent::_construct();

        if ($this->_isAllowedAction('Lofmp_StoreLocator::storelocator_save')) {
            $this->buttonList->update('save', 'label', __('Save StoreLocator'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->_isAllowedAction('Lofmp_StoreLocator::storelocator_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete StoreLocator'));
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
        if ($this->_coreRegistry->registry('storelocator_storelocator')->getId()) {
            return __("Edit StoreLocator '%1'", $this->escapeHtml($this->_coreRegistry->registry('storelocator_storelocator')->getTitle()));
        } else {
            return __('New StoreLocator');
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
        return $this->getUrl('cms/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = '
            function toggleEditor() {
                if (tinyMCE.getInstanceById("page_content") == null) {
                    tinyMCE.execCommand("mceAddControl", false, "page_content");
                } else {
                    tinyMCE.execCommand("mceRemoveControl", false, "page_content");
                }
            };
            require([
                "jquery",
                "Lofmp_StoreLocator/js/jquery.minicolors.min"],function($){

                    $(".minicolors").each( function() {
                        $(this).minicolors({
                            control: $(this).attr("data-control") || "hue",
                            defaultValue: $(this).attr("data-defaultValue") || "",
                            format: $(this).attr("data-format") || "hex",
                            keywords: $(this).attr("data-keywords") || "",
                            inline: $(this).attr("data-inline") === "true",
                            letterCase: $(this).attr("data-letterCase") || "lowercase",
                            opacity: $(this).attr("data-opacity"),
                            position: $(this).attr("data-position") || "bottom left",


                            change: function(value, opacity) {
                                if( !value ) return;
                                if( opacity ) value += ", " + opacity;
                                if( typeof console === "object" ) {
                                    console.log(value);
                                }
                            },

                        });
                    });
            });
        ';

        return parent::_prepareLayout();
    }
}
