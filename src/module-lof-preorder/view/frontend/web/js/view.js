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
    "jquery",
    "jquery/ui",
    'mage/translate'
    ], function ($,jUI, Translate) {
        'use strict';
        $.widget('preorder.view', {
            options: {},
            _original: {
                preorderText: '',
                buttonText: '',
                payHtml: '',
                msg: ''
            },
            _enabled: false,
            _saveOriginal: function () {
                $(".product-info-price").after('<div class="lofpreorder-msg-box"></div>');
                var preorderMsgElement = $('.lofpreorder-msg-box');
                this.options.preorderMsgElement = preorderMsgElement;

                var addtocartButton = '#product-addtocart-button';
                var addtocartButtonSpan = '#product-addtocart-button span';
                if(typeof(this.options.addtocartButton) !== "undefined"){
                    addtocartButton = this.options.addtocartButton;
                }
                if(typeof(this.options.addtocartButtonSpan) !== "undefined"){
                    addtocartButtonSpan = this.options.addtocartButtonSpan;
                }
                this.options.addtocartButton = addtocartButton;
                this.options.addtocartButtonSpan = addtocartButtonSpan;
                this.options.availabilityElement = this.options.availabilityElement
                    ? $(this.options.preorderTextPosition)
                    : $(".product-info-main").find('.stock');
                if (this.options.availabilityElement) {
                    this._original.preorderText = this.options.flag
                        ? this.options.preorderText
                        : this.options.availabilityElement.text();
                }

                if ($(this.options.addtocartButton).length) {
                    var originalButtonElement = $(this.options.addtocartButtonSpan);
                    if (originalButtonElement.length > 0) {
                        this._original.buttonText = $(this.options.addtocartButtonSpan).text();
                    } else {
                        this._original.buttonText = originalButtonElement
                    }
                }
                this._original.msg = this.options.msg;
                this._original.payHtml = this.options.payHtml;
            },
            _setPreOrderLabel: function()
            {
                $(this.options.preorderMsgElement).html("");
                var preorderTextPosition = ".stock span";
                if(typeof(this.options.preorderTextPosition) !== "undefined"){
                    preorderTextPosition = this.options.preorderTextPosition;
                }
                var msg = this.options.msg;
                msg = msg.replace(/\n/g, "<br />");
                if (this.options.flag == 1) {
                    $(this.options.preorderMsgElement).html(this.options.payHtml);
                    $(this.options.preorderMsgElement).append(msg);
                }
                $(this.options.addtocartButton).attr("title",this.options.buttonText);
                $(this.options.addtocartButtonSpan).text(this.options.buttonText);
                $(preorderTextPosition).text(this.options.preorderText);
            },
            _setDefaultLabel: function()
            {
                $(this.options.preorderMsgElement).html("");
                var msg = this._original.msg;
                var addToCartButtonLabel = this._original.buttonText;
                var stockLabel = this._original.preorderText;
                var preOrderLabel = Translate("Pre Order");
                var preorderText = Translate("Pre Order");
                var preorderTextPosition = ".stock span";
                if(typeof(default_preorder_label) !== "undefined" && default_preorder_label){
                    preOrderLabel = default_preorder_label;
                }
                if(typeof(this.options.buttonText) !== "undefined"){
                    preOrderLabel = this.options.buttonText;
                }
                if(typeof(this.options.preorderText) !== "undefined"){
                    preorderText = this.options.preorderText;
                }
                if(typeof(this.options.preorderTextPosition) !== "undefined"){
                    preorderTextPosition = this.options.preorderTextPosition;
                }
                msg = msg.replace(/\n/g, "<br />");
                if (this.options.flag == 1) {
                    $(this.options.preorderMsgElement).html(this.options.payHtml);
                    $(this.options.preorderMsgElement).append(msg);
                }
                $(this.options.addtocartButton).attr("title",addToCartButtonLabel);
                $(this.options.addtocartButtonSpan).text(addToCartButtonLabel);
                $(preorderTextPosition).text(stockLabel);
            },
            _create: function () {
                var self = this;
                this._saveOriginal();
                $(document).ready(function () {
                    var addtocartButton = self.options.addtocartButton;
                    var addtocartButtonSpan = self.options.addtocartButtonSpan;
                    var url = self.options.url;
                    var productId = self.options.productId;
                    var addToCartButtonLabel = $(self.options.addtocartButtonSpan).text();
                    var count = 0;
                    var isPreorder = self.options.flag;
                    if (isPreorder == 1) {
                        self._setPreOrderLabel();
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
                    $('#product-options-wrapper .super-attribute-select').change(function () {
                        self._setDefaultLabel();
                        var flag = 1;
                        setTimeout(function () {
                            $("#product_addtocart_form input[type='hidden']").each(function () {
                                $('#product-options-wrapper .super-attribute-select').each(function () {
                                    if ($(this).val() == "") {
                                        flag = 0;
                                    }
                                });
                                var name = $(this).attr("name");
                                if (name == "selected_configurable_option") {
                                    var productId = $(this).val();
                                    if (productId != "" && flag ==1) {
                                        $(".lof-loading-mask").removeClass("lof-display-none");
                                        $.ajax({
                                            url: url,
                                            type: 'POST',
                                            data: { product_id : productId },
                                            dataType: 'json',
                                            success: function (data) {
                                                if (data.preorder == 1) {
                                                    self._setPreOrderLabel();
                                                    isPreorder = 1;
                                                    $(self.options.preorderMsgElement).html(data.payHtml);
                                                    $(self.options.preorderMsgElement).append(data.msg);
                                                } else {
                                                    self._setDefaultLabel();
                                                    isPreorder = 0;
                                                }
                                                $(".lof-loading-mask").addClass("lof-display-none");
                                            }
                                        });
                                    }
                                }
                            });
                        }, 0);
                    });

                    $('body').on('click', '#product-options-wrapper .swatch-option', function () {
                        var flag = 1;
                        var attributeInfo = {};
                        self._setDefaultLabel();
                        setTimeout(function () {
                            $('#product-options-wrapper .swatch-attribute').each(function () {
                                if($(this).attr('option-selected')) {
                                    var selectedOption = $(this).attr("option-selected");
                                    var attributeId = $(this).attr("data-attribute-id");
                                    attributeInfo[attributeId] = selectedOption;
                                } else {
                                    flag = 0;
                                }
                            });
                            if(flag == 1) {
                                $(".lof-loading-mask").removeClass("lof-display-none");
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: { type : 1, product_id : productId, info : attributeInfo },
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data.preorder == 1) {
                                            self._setPreOrderLabel();
                                            isPreorder = 1;
                                            $(self.options.preorderMsgElement).html(data.payHtml);
                                            $(self.options.preorderMsgElement).append(data.msg);
                                        } else {
                                            self._setDefaultLabel();
                                            isPreorder = 0;
                                        }
                                        $(".lof-loading-mask").addClass("lof-display-none");
                                    }
                                });
                            }
                        }, 0);
                    });
                });
            }
        });
        return $.preorder.view;
    });
