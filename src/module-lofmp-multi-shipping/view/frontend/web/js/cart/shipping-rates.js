/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

define(
    [
        'jquery',
        'ko',
        'underscore',
        'uiComponent',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/checkout-data'
    ],
    function (
        $,
        ko,
        _,
        Component,
        shippingService,
        priceUtils,
        quote,
        selectShippingMethodAction,
        checkoutData
    ) {
        'use strict';
        return Component.extend(
            {
                defaults: {
                    template: 'Lofmp_MultiShipping/cart/shipping-rates'
                },
                isVisible: ko.observable(!quote.isVirtual()),
                isLoading: shippingService.isLoading,
                shippingRates: shippingService.getShippingRates(),
                shippingRateGroups: ko.observableArray([]),
                // selectedShippingMethod: ko.computed(
                //     function () {
                //         var seletecdmth = quote.shippingMethod() ? quote.shippingMethod().method_code : null;
                //         if (seletecdmth) {
                //             var METHOD_SEPARATOR = ':';
                //             var methods = seletecdmth.split(METHOD_SEPARATOR);
                //             for (var i = 0; i < methods.length; i++) {
                //                 if (document.getElementById(methods[i]) && document.getElementById('seller_rates_' + seletecdmth)) {
                //                     document.getElementById(methods[i]).checked = true;
                //                 }
                //             }
                //         }
                //         var inputs = document.getElementsByClassName('radio');
                //         for (var i = 0; i < inputs.length; i++) {
                //             inputs[i].disabled = false;
                //         }
                //         return quote.shippingMethod() ? quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] : null;
                //     }
                // ),

                /**
                 * @override
                 */
                initObservable: function () {
                    var self = this;
                    this._super();
                    this.shippingRates.subscribe(
                        function (grates) {
                            var items = quote.getItems();
                            var count = 0;
                            self.shippingRateGroups([]);
                            $.each(items, function (index, value) {
                                var seller_id = [];
                                seller_id = value.product.seller_id;
                                if (self.shippingRateGroups.indexOf(seller_id) === -1) {
                                    self.shippingRateGroups.push(seller_id);
                                    count++;
                                }
                            });
                            self.shippingRateGroups.sort();
                            window.sellerArray = count;
                            _.each(
                                grates, function (rate) {
                                    if (rate['carrier_code'] == 'seller_rates') {
                                        if (rate['method_code'].indexOf("|") !== -1) {
                                            var result = rate['method_code'].split('|');
                                            var seller = result['0'] ? result['0'] : '';
                                            var method_code = result['1'] ? result['1'] : '';
                                            rate['seller_id'] = seller;
                                            rate['method_code'] = method_code;
                                        }
                                    }
                                }
                            );
                        }
                    );
                    return this;
                },
                getSellerName: function (seller_id) {
                    var theRate = this.getRatesForGroup(seller_id)
                    if (theRate && theRate[0]) {
                        return theRate[0].carrier_title;
                    }
                },
                getRatesForGroup: function (shippingRateGroupTitle) {
                    return _.filter(
                        this.shippingRates(), function (rate) {
                            if (typeof shippingRateGroupTitle != "undefined") {
                                if (rate['seller_id'] == 'admin' && shippingRateGroupTitle == '0') {
                                    return true;
                                }
                                return shippingRateGroupTitle === rate['seller_id'];
                            }
                        }
                    );
                },

                /**
                 * Format shipping price.
                 *
                 * @returns {String}
                 */
                getFormattedPrice: function (price) {
                    return priceUtils.formatPrice(price, quote.getPriceFormat());
                },
                selectShippingMethod: function (shippingMethod) {
                    selectShippingMethodAction(shippingMethod);
                    checkoutData.setSelectedShippingRate(shippingMethod.carrier_code + '_' + shippingMethod.method_code);
                    return true;
                },

                selectVirtualMethod: function (shippingMethod) {
                    var flagg = true;
                    var METHOD_SEPARATOR = ':';
                    var rates = [];
                    var flag = false;
                    var count = 0;
                    var price = 0;
                    $('.seller-rates').each(function () {
                        var $seller_rates = $(this);
                        var $radio = $seller_rates.find('.radio:checked');
                        if ($radio.is(':checked')) {
                            count++;
                            flag = true;
                            rates.push($radio.val());
                            price += Number($radio.attr('price'));
                        }
                    })
                    var total = price.toFixed(2);
                    var totalPrice = priceUtils.formatPrice(total, quote.getPriceFormat());
                    window.selected = count;
                    if (!flag) {
                        flagg = false;
                    }
                    if (flagg) {
                        var rate = '';
                        for (var i = 0; i < rates.length; i++) {
                            if (i != rates.length) {
                                rates[i] = rates[i].substring(13);
                            }
                            if (i == 0) {
                                rate = rates[i];
                            } else {
                                rate = rate + METHOD_SEPARATOR + rates[i];
                            }
                        }
                        if (count == window.sellerArray) {
                            var selectShippingMethod = Object.assign({}, shippingMethod);
                            selectShippingMethod.method_code = rate;
                            selectShippingMethod.method_title = window.checkoutConfig.sellermultishipping.method_title;
                            selectShippingMethod.carrier_title = window.checkoutConfig.sellermultishipping.carrier_title;
                            selectShippingMethodAction(selectShippingMethod);
                            checkoutData.setSelectedShippingRate(rate);
                        }
                    }
                    return true;
                }
            }
        );
    }
);
