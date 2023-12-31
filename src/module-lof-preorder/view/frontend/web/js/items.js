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
    ], function ($, jUI, Translate) {
        'use strict';
        $.widget('preorder.items', {
            options: {},
            _create: function () {
                var self = this;
                $(document).ready(function () {
                    var options = self.options.option;
                    var info = self.options.info;
                    var flag = self.options.flag;
                    var orderId = self.options.orderid;
                    var incrementId = self.options.incrementId;
                    var preorder_warning = self.options.preorder_warning;
                    var preorderCompleteProductId = self.options.preorderCompleteProductId;
                    var url = self.options.url;
                    var formKey = "";
                    $("body input[type='hidden']").each(function () {
                        var name = $(this).attr("name");
                        if (name == "form_key") {
                            formKey = $(this).val();
                        }
                    });
                    if (flag == 1) {
                        var msgBox = $('<div/>').addClass("lof-msg-box lof-info").text(preorder_warning);
                        $(".page-title-wrapper").append(msgBox);
                    }
                    var count = 0;
                    $("#my-orders-table tbody > tr[id^='order-item-row']").each(function () {
                        var msg = info[count]['msg'];
                        if (info[count]['available'] == 1 && info[count]['preorder'] == 1) {
                            $(this).find("td:last-child").append('<button class="lof-preorder-complete action tocart primary" data-key="'+count+'" title="'+Translate("Complete Preorder")+'" type="submit"><span>'+Translate("Complete Preorder")+'</span></button>');
                        }
                        if (info[count]['preorder'] == 1) {
                            $(this).find("td:first-child").append(Translate("Status: ")+"<strong>"+Translate("Preorder Pending")+"</strong><br/>"+msg);
                        }
                        if (info[count]['preorder'] == 2) {
                            $(this).find("td:first-child").append(Translate("Status: ")+"<strong>"+Translate("Preorder Completed")+"</strong>");
                        }
                        count++;
                    });
                    $(document).on('click', '.lof-preorder-complete', function (event) {
                        $(".lof-loading-mask").removeClass("lof-display-none");
                        var option = {};
                        var key = $(this).attr("data-key");
                        var productId = info[key]['product_id'];
                        var itemId = info[key]['item_id'];
                        var qty = info[key]['qty'];
                        var name = info[key]['product_name'];
                        $.each(options, function (k, v) {
                            var optionId = v.id;
                            var optionTitle = v.title;
                            if (optionTitle == Translate('Product Name')) {
                                option[optionId] = name;
                            }
                            if (optionTitle == Translate('Order Refernce')) {
                                option[optionId] = incrementId;
                            }
                        });
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: { pro_id:productId, form_key:formKey, options:option, order_id:orderId, item_id : itemId, product : preorderCompleteProductId, qty:qty },
                            success: function (data) {
                                $(".lof-loading-mask").addClass("lof-display-none");
                            }
                        });
                    });
                });
            }
        });
        return $.preorder.items;
    });
