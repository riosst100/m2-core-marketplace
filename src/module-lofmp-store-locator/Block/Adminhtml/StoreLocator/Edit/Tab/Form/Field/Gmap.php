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
namespace Lofmp\StoreLocator\Block\Adminhtml\StoreLocator\Edit\Tab\Form\Field;

class Gmap extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    public function getElementHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $api_key = $objectManager->create('Lofmp\StoreLocator\Helper\Data')->getConfig('general/api_key');  

        $html = '';
        $elementId = $this->getHtmlId();
        $elementId = str_replace("_address_preview", "", $elementId);

        $mapElementId = 'map-'.$elementId;
        $addressElementId = 'storelocator_address';
        $latElementId = 'storelocator_lat';
        $lngElementId = 'storelocator_lng';

        $html .= '<br/><div id="'.$mapElementId.'" style="width:690px;height:400px">';
        $html .= '</div>';
        //$html .= '<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&libraries=places&key='.$api_key.'"></script>';
        $html .= '<script src="https://maps.googleapis.com/maps/api/js?key='.$api_key.'&libraries=places&callback=initMap" async defer></script>';
        $html .= '<script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById("'.$mapElementId.'"), {
                  center: {lat: -33.8688, lng: 151.2195},
                  zoom: 13,
                });
                var input = document.getElementById("'.$addressElementId.'");
                var lat = document.getElementById("'.$latElementId.'");
                var lng = document.getElementById("'.$lngElementId.'");

                

                var infowindow = new google.maps.InfoWindow();
                var marker = new google.maps.Marker({
                  map: map,
                  draggable: true,
                  position: {lat: -33.8688, lng: 151.2195},
                });
                // marker.setVisible(true);


                google.maps.event.addListener(marker,"drag",function(event){
                  document.getElementById("'.$latElementId.'").value = this.position.lat();
                  document.getElementById("'.$lngElementId.'").value = this.position.lng();

                  var geocoder = new google.maps.Geocoder; 
                  geocoder.geocode({"location": this.position}, function(results, status) { 
                    if (status === "OK") {
                      document.getElementById("'.$addressElementId.'").value = results[0].formatted_address;
                      infowindow.setContent(results[0].formatted_address);
                      infowindow.open(map, marker);
                    }
                  });
                });

                var autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.bindTo("bounds", map);

                autocomplete.addListener("place_changed", function() {
                  infowindow.close();
                  marker.setVisible(false);
                  var place = autocomplete.getPlace();

                  // If the place has a geometry, then present it on a map.
                  if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                  } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);  // Why 17? Because it looks good.
                  }
                 
                  marker.setPosition(place.geometry.location);
                  marker.setVisible(true);

                  var address = place.formatted_address;

                  document.getElementById("'.$latElementId.'").value = place.geometry.location.lat();
                  document.getElementById("'.$lngElementId.'").value = place.geometry.location.lng();

                  infowindow.setContent("<div><strong>" + place.name + "</strong><br>" + address);
                  infowindow.open(map, marker);
                });

              }
        </script>';
        return $html;
    }
}