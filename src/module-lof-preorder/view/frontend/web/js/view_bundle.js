/*
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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

define([
    'jquery',
    "jquery/ui",
    'mage/translate',
    'Lof_PreOrder/js/view'
], function($, jUI, Translate) {
    'use strict';
    $.widget('preorder.viewBundle', $.preorder.view, {
        options: {
            map: {},
            optionsData: {},
            checkedElements: {},
            availabilityElement: $(".product-info-main").first().find('.stock')
        },
        _create: function(){
            var self = this;
            this._saveOriginal();
            $(document).ready(function () {
                var addtocartButton = self.options.addtocartButton;
                var addtocartButtonSpan = self.options.addtocartButtonSpan;
                var addToCartButtonLabel = $(self.options.addtocartButtonSpan).text();
                var count = 0;
                var isPreorder = self.options.flag;
                if (isPreorder == 1) {
                    self._setPreOrderLabel();
                }
                var option;
                for(var optionId in self.options.optionsData) {
                    option = self.options.optionsData[optionId];
                    var $place = $($("#bundle-option-"+optionId+"-qty-input").parents('.field.option')[0]);

                    if(option.isRequired && option.isPreorder && option.isSingle) {
                        self._enableOrDisablePreorder({
                            mapId: optionId + "-" +option.selectionId,
                            optionId: optionId,
                            selectionId: option.selectionId
                        }, $place);
                    }
                }

                $(addtocartButton).click(function () {
                    count = 0;
                });

                $(addtocartButtonSpan).bind("DOMSubtreeModified",function () {
                    var title = $(this).text();
                    if (isPreorder == 1) {
                        if (title == addToCartButtonLabel) {
                            count++;
                            if (count == 1) {
                                self._setPreOrderLabel();
                            }
                        }
                    }
                });

                $('.bundle-options-wrapper .radio, .bundle-options-wrapper .bundle-option-select')
                    .change(function(event){
                        var element = event.currentTarget;
                        if (element.checked) {
                            var elementInfo = self._getElementInfo(element);
                            var $place = $($(element).parents('.field.option')[0]);
                            self._enableOrDisablePreorder(elementInfo, $place);
                        }
                        return;
                });

                $('.bundle-options-wrapper .checkbox')
                    .change(function(event){
                        var element = event.currentTarget;
                        var $place = $($(element).parents('.field.option')[0]);
                        var elementInfo = self._getElementInfo(element);
                        var isSelect = $(element).is(":checked")
                        self._enableOrDisablePreorderMultiselection(elementInfo, $place, isSelect);
                        return;
                    });

                $('.bundle-options-wrapper .multiselect')
                    .change(function(event){
                        var element = event.currentTarget;
                        var $place = $($(element).parents('.field.option')[0]);
                        var elementInfo = self._getElementInfo(element);
                        var isSelect;
                        $.each($(element).find('option'), function(key, option){
                            isSelect = false;
                            elementInfo.selectionId = $(option).val();
                            elementInfo.mapId = elementInfo.optionId + "-" + elementInfo.selectionId;
                            $.each($(element).val(), function(valueKey, value){
                                if($(option).val() == value) {
                                    isSelect = true;
                                    return;
                                }
                            });
                            self._enableOrDisablePreorderMultiselection(elementInfo, $place, isSelect);
                        });
                        return;
                    });
            });
        },
        _getElementInfo: function(element){
            var elementInfo = {
                mapId: 0,
                optionId: 0,
                selectionId: 0
            };
            elementInfo.mapId = element.id.substring(element.id.indexOf("bundle-option-")+String("bundle-option-").length);
            if($(element).prop('tagName').toLowerCase() == "select") {
                elementInfo.optionId = elementInfo.mapId;
                elementInfo.selectionId = $(element).val();
                elementInfo.mapId += "-" + elementInfo.selectionId;
            } else {
                if(elementInfo.mapId.indexOf("-") > -1) {
                    elementInfo.optionId = elementInfo.mapId.substring(0, elementInfo.mapId.indexOf("-"));
                    elementInfo.selectionId = elementInfo.mapId.substring(elementInfo.mapId.indexOf("-")+1);
                } else {
                    elementInfo.optionId = elementInfo.mapId;
                }
            }
            return elementInfo;
        },
        _enableOrDisablePreorder: function (elementInfo, $place) {
            if(this.options.map[elementInfo.mapId]){
                var $container = $('#bundle-option-'+elementInfo.optionId+'-preorder-note');
                if($container.length == 0) {
                    $place.append('<div class="field" style="margin-top: 10px"><span id="bundle-option-'+elementInfo.optionId+'-preorder-note">'+this.options.map[elementInfo.mapId].msg+'</span></div>');
                } else {
                    $container.html(this.options.payHtml);
                    $container.append(this.options.map[elementInfo.mapId].msg);
                }
                this.options.checkedElements[elementInfo.optionId] = true;
                this.options.buttonText = this.options.map[elementInfo.mapId].buttonText;
                this.options.msg = this.options.map[elementInfo.mapId].msg;
                this.options.flag = this.options.map[elementInfo.mapId].flag;
                this._setPreOrderLabel();
            } else {
                var $container = $('#bundle-option-'+elementInfo.optionId+'-preorder-note');
                if($container.length > 0) {
                    $container.html('');
                }
                this.options.checkedElements[elementInfo.optionId] = false;

                var counter = 0;

                for (var key in this.options.checkedElements) {
                    if(this.options.checkedElements[key]) {
                        counter++;
                    }
                }
                if(counter == 0) {
                    this._setDefaultLabel();
                }
            }
        },
        _enableOrDisablePreorderMultiselection: function(elementInfo, $place, isSelect) {
            if(this.options.map[elementInfo.mapId] && isSelect){
                var $container = $('#bundle-option-'+elementInfo.mapId+'-preorder-note');
                if($container.length == 0) {
                    $place.append('<div class="field" style="margin-top: 10px"><span id="bundle-option-'+elementInfo.mapId+'-preorder-note">'+this.options.map[elementInfo.mapId].msg+'</span></div>');
                } else {
                    $container.html(this.options.payHtml);
                    $container.append(this.options.map[elementInfo.mapId].msg);
                }
                this.options.checkedElements[elementInfo.mapId] = true;
                this.options.buttonText = this.options.map[elementInfo.mapId].buttonText;
                this.options.msg = this.options.map[elementInfo.mapId].msg;
                this.options.flag = this.options.map[elementInfo.mapId].flag;
                this._setPreOrderLabel();
            } else {
                var $container = $('#bundle-option-'+elementInfo.mapId+'-preorder-note');
                if($container.length > 0) {
                    $container.html('');
                }
                this.options.checkedElements[elementInfo.mapId] = false;
                var counter = 0;
                for (var key in this.options.checkedElements) {
                    if(this.options.checkedElements[key]) {
                        counter++;
                    }
                }
                if(counter == 0) {
                    this._setDefaultLabel();
                }
            }
        }
    });
    return $.preorder.viewBundle;
});
