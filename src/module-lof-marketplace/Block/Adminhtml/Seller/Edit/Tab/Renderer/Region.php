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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab\Renderer;

use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Region extends \Magento\Backend\Block\AbstractBlock implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
    * @var \Magento\Directory\Helper\Data
    */
    protected $_directoryHelper;

        /**
     * @var SecureHtmlRenderer
     */
    private $secureRenderer;

    /**
    * @param \Magento\Backend\Block\Context $context
    * @param \Magento\Directory\Helper\Data $directoryHelper
    * @param SecureHtmlRenderer $secureRenderer
    * @param array $data
    */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        SecureHtmlRenderer $secureRenderer,
        array $data = []
    ) {
        $this->_directoryHelper = $directoryHelper;
        $this->secureRenderer = $secureRenderer;
        parent::__construct($context, $data);
    }

    /**
    * Output the region element and javasctipt that makes it dependent from country element
    *
    * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
    * @return string
    *
    * @SuppressWarnings(PHPMD.UnusedLocalVariable)
    */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($country = $element->getForm()->getElement('country_id')) {
            $countryId = $country->getValue();
        } else {
            return $element->getDefaultHtml();
        }
        $regionId = $element->getForm()->getElement('region_id')->getValue();
        $element->setClass('admin__field field field-region_id');
        $element->setRequired(false);
        $selectName = $element->getName();
        $selectId = $element->getHtmlId() . '_id';
        $html = '';
        $html .= '<div class="'.$element->getClass().'">';
        $html .= $element->getLabelHtml();
        $html .= '<div>';
        $html .= '<select id="'.$selectId.'" name="region_id" title="'.__("State/Province").'" class="required-entry input-text" style="display:none;">';
        $html .= '<option value="">' . __('Please select') . '</option>';
        $html .= '</select>';
        $html .= '</div>';
        $scriptString = <<<HTML
require(["jquery", "prototype", "mage/adminhtml/form"], function(jQuery){
    var old_region_name = jQuery("#seller_region").val();
    \$("{$selectId}").setAttribute("defaultValue", "{$regionId}");
    new regionUpdater("{$country->getHtmlId()}", "{$element->getHtmlId()}", "{$selectId}", {$this->_directoryHelper->getRegionJson()});
    jQuery("#{$selectId}").on("change", function() {
        if (jQuery(this).val() && jQuery("#seller_region").length > 0) {
            jQuery("#seller_region").val(jQuery("#{$selectId} option:selected").text());
        } else if(jQuery("#seller_region").length > 0) {
            jQuery("#seller_region").val(old_region_name);
        }
    })
})
HTML;
        $html .= /* @noEscape */ $this->secureRenderer->renderTag('script', [], $scriptString, false);
        $html .= "</div>";
        return $html;
    }
}
