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
 /*jshint jquery:true*/
 define([
    "jquery",
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'mage/template'
], function ($, $t, alert, mageTemplate) {
    'use strict';
    $.widget('mage.sellerOrderShipment', {
        _create: function () {
            var self = this;
            $('#lof-mp-tracking-carrier').change(function () {
                if ($('select#lof-mp-tracking-carrier option:selected').val() != 'custom') {
                    var val = $('select#lof-mp-tracking-carrier option:selected').text();
                    $('#lof-mp-tracking-title').attr('value', $.trim(val));
                } else {
                    $('#lof-mp-tracking-title').attr('value', '');
                }
            });
            $('body').on('click', '.lof-mp-tracking-action-delete', function (e) {
                var thisObj = $(this);
                $.ajax({
                    url: thisObj.attr('data-url'),
                    type: "POST",
                    showLoader: true,
                    success:function ($data) {
                        if ($data.error) {
                            alert({
                                content: $data.message
                            });
                        } else {
                            thisObj.parents('tr').remove();
                        }
                    },
                    error: function (response) {
                        alert({
                            content: self.options.ajaxErrorMessage
                        });
                    }
                });
            });
            $('#lof-mp-tracking-add').click(function (e) {
                $.ajax({
                    url: self.options.addTrackingAjaxUrl,
                    type: "POST",
                    data: {
                        carrier: $('#lof-mp-tracking-carrier').val(),
                        title: $('#lof-mp-tracking-title').val(),
                        number: $('#lof-mp-tracking-number').val()
                    },
                    showLoader: true,
                    success:function ($data) {
                        if ($data.error) {
                            alert({
                                content: $data.message
                            });
                        } else {
                            var progressTmpl = mageTemplate('#sellerOrderShipmentTemplate'),tmpl;
                            tmpl = progressTmpl({
                                data: {
                                    carrier: $data.carrier,
                                    title: $data.title,
                                    number: $data.number,
                                    numberclass: $data.numberclass,
                                    numberclasshref: $data.numberclasshref,
                                    trackingPopupUrl: $data.trackingPopupUrl,
                                    trackingDeleteUrl: $data.trackingDeleteUrl
                                }
                            });
                            $('#lof-mp-shipment-tracking-info-tbody').append(tmpl);
                            $('#lof-mp-tracking-carrier').val('custom');
                            $('#lof-mp-tracking-title').val('');
                            $('#lof-mp-tracking-number').val('');
                        }
                    },
                    error: function (response) {
                        alert({
                            content: self.options.ajaxErrorMessage
                        });
                    }
                });
            });
        }
    });
    return $.mage.sellerOrderShipment;
});
