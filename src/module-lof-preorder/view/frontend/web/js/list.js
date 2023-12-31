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
        $.widget('preorder.list', {
            options: {},
            _create: function () {
                var self = this;
                $(document).ready(function () {
                    var preorderInfo = self.options.preorderInfo;
                    var count = 0;
                    var isPreorder = 0;
                    var url = preorderInfo['url'];

                    var preOrderLabel = Translate("Pre Order");
                    if(typeof(default_preorder_label) !== "undefined" && default_preorder_label){
                        preOrderLabel = default_preorder_label;
                    }
                    if(typeof(preorderInfo['buttonText']) !== "undefined"){
                        preOrderLabel = preorderInfo['buttonText'];
                    }
                        $(".products ol.product-items > li.product-item").each(function () {
                            var productId = $(this).find('input[name="product"]').val();
                            var addToCartButtonLabel = $(this).find('.tocart').find('span').text();
                            var a = $(this);

                            if ((typeof preorderInfo[productId] != 'undefined') && preorderInfo[productId]['preorder'] == 1) {
                                setPreOrderLabel($(this));
                                setPreOrderMsg($(this));
                            }
                            $(this).find('.swatch-option').on('click', function () {
                                var flag = 1;
                                var attributeInfo = {};
                                $(".lof-msg-box").remove();
                                setTimeout(function () {
                                    $('.swatch-opt-'+productId+' .swatch-attribute').each(function () {
                                        if($(this).attr('option-selected')) {
                                            var selectedOption = $(this).attr("option-selected");
                                            var attributeId = $(this).attr("attribute-id");
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
                                                console.log(data);
                                                if (data.preorder == 1) {
                                                    setPreOrderLabel(a);
                                                    setPreOrderMsg(a);
                                                    isPreorder = 1;
                                                } else {
                                                     setDefaultLabel(a,addToCartButtonLabel);
                                                    isPreorder = 0;
                                                }
                                                $(".lof-loading-mask").addClass("lof-display-none");
                                            }
                                        });
                                    }
                                }, 0);
                            });
                        });


                        $('.action.tocart').click(function () {
                            var url = $(this).parents(".product-item-info").find('input[name="product"]').val();
                            isPreorder = preorderInfo[url]['preorder'];
                            count = 0;
                        });
                        $('.action.tocart span').bind("DOMSubtreeModified",function () {
                            var title = $(this).text();
                            if (isPreorder == 1) {
                                if (title == Translate("Add to Cart")) {
                                    count++;
                                    if (count == 1) {
                                        $(this).parent().attr("title",preOrderLabel);
                                        $(this).text(preOrderLabel);
                                    }
                                }
                            }
                        });
                        function setPreOrderLabel(currentObject)
                        {
                            currentObject.find(".action.tocart.primary").attr("title",preOrderLabel);
                            currentObject.find(".action.tocart.primary").find("span").text(preOrderLabel);
                        }
                        function setPreOrderMsg(currentObject)
                        {
                            var url = $(currentObject).find(".product-item-info").find('input[name="product"]').val();
                            if(typeof(preorderInfo[url]) != "undefined"){
                                var msg = preorderInfo[url]['msg'];
                                msg = msg.replace(/\n/g, "<br />");
                                if(typeof(msg) != "undefined" && msg.length > 0){
                                    currentObject.find(".product-item-info").find('[data-role="priceBox"]').after(msg);
                                }
                            }
                        }
                        function setDefaultLabel(currentObject,text)
                        {
                            currentObject.find(".action.tocart.primary").attr("title",text);
                            currentObject.find(".action.tocart.primary").find("span").text(text);
                        }
                });
            }
        });
        return $.preorder.list;
    });

