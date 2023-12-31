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

/*global define*/
define(
    [
        'jquery',
        "underscore",
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'Magento_Catalog/js/price-utils',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service',
        'mage/url'
    ],
    function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        priceUtils,
        $t,
        rate_service,
        url
    ) {
        'use strict';
        return function (target) {
            if (window.checkoutConfig.sellermultishipping === undefined) {
                return target.extend({});
            }
            return target.extend({
                    defaults: {
                        template: 'Lofmp_MultiShipping/shipping'
                    },
                    shippingRateGroups: ko.observableArray([]),
                    total: ko.observable(0),
                    initialize: function () {
                        var self = this;
                        this._super();

                        this.rates.subscribe(
                            function (grates) {
                                var items = quote.getItems();
                                var count = 0;
                                self.shippingRateGroups([]);
                                $.each(items, function (index, value) {
                                    var seller_id = [];
                                    seller_id = typeof(value.seller_id) != undefined && value.seller_id ? value.seller_id : value.product.seller_id;
                                    if (self.shippingRateGroups.indexOf(seller_id) === -1) {
                                        self.shippingRateGroups.push(seller_id);
                                        count++;
                                    }
                                });
                                self.shippingRateGroups.sort();
                                window.sellerArray = count;
                                _.each(
                                    grates, function (rate) {
                                        if (rate['carrier_code'] === 'seller_rates') {
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
                    initElement: function (element) {
                        if (element.index === 'shipping-address-fieldset') {
                            shippingRatesValidator.bindChangeHandlers(element.elems(), false);
                        }
                    },
                    getFormattedPrice: function (price) {
                        return priceUtils.formatPrice(price, quote.getPriceFormat());
                    },
                    getProductBySeller: function (seller_id) {
                        var list = [];
                        var items = quote.getItems();
                        $.each(items, function (index, value) {
                            if (list.indexOf(value.product.name) === -1 && value.product.seller_id == seller_id) {
                                list.push(value.product.name + ' x ' + value.qty);
                            }
                        });
                        return _.filter(
                            list, function (item) {
                                return true;
                            }
                        );
                    },
                    getSellerName: function (seller_id) {
                        var theRate = this.getRatesForGroup(seller_id)
                        if (theRate && theRate[0]) {
                            return theRate[0].carrier_title;
                        }
                    },
                    getRatesForGroup: function (shippingRateGroupTitle) {
                        return _.filter(
                            this.rates(), function (rate) {
                                if (typeof shippingRateGroupTitle != "undefined") {
                                    if (rate['seller_id'] == 'admin' && shippingRateGroupTitle == '0') {
                                        return true;
                                    }
                                    return shippingRateGroupTitle === rate['seller_id'];
                                }
                            }
                        );
                    },
                    selectVirtualMethod: function (shippingMethod) {
                        var flagg = true;
                        var METHOD_SEPARATOR = ':';
                        var rates = [];
                        var flag = false;
                        var count = 0;
                        var price = 0;
                        var textPrice = $t('Total Shipping Cost: ')
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
                        $("#total-price").html(textPrice + totalPrice);
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
                                selectShippingMethod.amount = price;
                                selectShippingMethodAction(selectShippingMethod);
                                checkoutData.setSelectedShippingRate(rate);
                            }
                        }
                        return true;
                    },
                    validateShippingInformation: function () {
                        var flagg = true;
                        var rates = new Array();
                        jQuery('.seller-rates').each(
                            function(indx,elm){
                                var flag = false;
                                jQuery(elm).find('.radio').each(
                                    function(i,inpt){
                                        if(inpt.checked) {
                                            flag = true;
                                            rates.push(inpt.value);
                                        }
                                    }
                                );
                                if(!flag) {
                                    flagg = false;
                                }
                            }
                        );
                        if(!flagg) {
                            this.errorValidationMessage(
                                $t('Please select shipping method for each seller.')
                            );
                            return false;
                        }
                        return this._super();
                    }
                }
            );
        }
    }
);
