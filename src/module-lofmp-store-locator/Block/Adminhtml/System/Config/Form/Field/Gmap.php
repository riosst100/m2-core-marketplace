<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\Adminhtml\System\Config\Form\Field;

class Gmap extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lofmp\StoreLocator\Helper\Data $storeHelper
    ) {
        parent::__construct($context);
        $this->storeHelper = $storeHelper;
    }

    /**
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        $googleApi = $this->storeHelper->getConfig('general/api_key');

        $html = $element->getElementHtml();
        $elementId = $element->getHtmlId();
        $elementId = str_replace("_address_preview", "", $elementId);
        $latElementId = $elementId.'_location_lat';
        $lngElementId = $elementId.'_location_lng';
        $radiusElementId = $elementId.'_radius';
        $addressElementId = $element->getHtmlId();

        $html .= '<br/><div id="map-'.$element->getHtmlId().'" style="width:600px;height:400px; margin-top: 10px;">';
        $html.= '</div>';
        $html.= '<script>
        require([
            "jquery",
            "https://maps.googleapis.com/maps/api/js?key=' . $googleApi . '&sensor=false&libraries=places",
            "Lofmp_StoreLocator/js/locationpicker.jquery"
        ],function(){
                jQuery(window).load(function(){
                    jQuery("#map-'.$element->getHtmlId().'").locationpicker({
                        location: {latitude: $("'.$latElementId.'").value, longitude: $("'.$lngElementId.'").value},
                        radius: 100,
                        enableAutocomplete: true,
                        inputBinding: {
                            latitudeInput: jQuery("#'.$latElementId.'"),
                            longitudeInput: jQuery("#'.$lngElementId.'"),
                            locationNameInput: jQuery("#'.$addressElementId.'"),
                            radiusInput: 0
                        }
                    });
                });
            });
        </script>';
        return $html;
    }
}