/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'jquery',
        'mage/translate',
        'mage/calendar',
        'moment',
        'mageUtils',
        'Magento_Checkout/js/model/quote',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/url-builder',
        'mage/url',
        'Magento_Customer/js/model/customer',
        'mage/storage',
    ],
    function (
        ko,
        Component,
        _,
        $,
        $t,
        calendar,
        moment,
        utils,
        quote,
        globalMessageList,
        urlBuilder,
        mageurl,
        customer,
        storage
    ) {
        'use strict';
        if (typeof(window.checkoutConfig.delivery_slot) == 'undefined' || ( typeof(window.checkoutConfig.delivery_slot.enable) !== 'undefined' && window.checkoutConfig.delivery_slot.enable == 0)) {
            return Component.extend({
                defaults: {
                    template: 'Lofmp_DeliverySlot/deliveryslot/empty'
                }
            })
        }
        var deferred = $.Deferred();
        return Component.extend({
            defaults: {
                template: 'Lofmp_DeliverySlot/deliveryslot/deliveryslot-information'
            },
            targetDate : ko.observable(),
            availableSlots : ko.observableArray(),

            /**
             * Init component
             */
            initialize: function () {
                this._super();
                this.deliveryInfo = "";
                //this.availableSlots = ko.observableArray(['08:00-09:00', '09:00-12:00', '04:00-06:00']),
                    ko.bindingHandlers.datepicker = {
                        init: function (element, valueAccessor, allBindingsAccessor) {
                            var $el = $(element);
                            //initialize datepicker with some optional options
                            var options = { minDate: new Date()};
                            $el.datepicker(options);
                            var writable = valueAccessor();
                            if (!ko.isObservable(writable)) {
                                var propWriters = allBindingsAccessor()._ko_property_writers;
                                if (propWriters && propWriters.datepicker) {
                                    writable = propWriters.datepicker;
                                } else {
                                    return;
                                }
                            }
                            writable($(element).datepicker("getDate"));
                            /* Handle the field changing */
                            ko.utils.registerEventHandler(element, "change", function () {
                                writable($el.datepicker("getDate"));
                            });

                            /* Handle disposal (if KO removes by the template binding) */
                            ko.utils.domNodeDisposal.addDisposeCallback(element, function () {
                                $elem.datepicker("destroy");
                            });


                        },


                        update: function (element, valueAccessor) {
                            var widget = $(element).data("datepicker");
                            var value = ko.utils.unwrapObservable(valueAccessor());
                            var  $elem = $(element);
                            //this.availableSlots = [];
                            var current = $elem.datepicker("getDate");
                            if (value - current !== 0) {
                                $elem.datepicker("setDate", value);
                            }
                        }
                    };

            },
            finalSlots: function (response) {
                    var selectedDate = $('#targetDate').val();
                    this.availableSlots.removeAll();
                    var j=0;
                    var slots = [];
                    for (var i=0; i < response.length; i++) {
                        if ((new Date(response[i].date).getTime()) == (new Date(selectedDate).getTime())) {
                            var existedSlots = response[i].slots;
                            for (var j=0; j < existedSlots.length; j++) {
                                //slots[j] = (existedSlots[j].start_time +'-' + existedSlots[j].end_time);
                                var slotdisable = existedSlots[j].current_status;
                                if (slotdisable==1) {
                                    slotdisable = false;
                                } else {
                                    slotdisable = true;
                                }
                                slots[j] = { name: existedSlots[j].start_time +'-' + existedSlots[j].end_time, value: existedSlots[j].slot_id,disable: ko.observable(slotdisable)};
                            }
                        }
                    }
                    if (slots.length >0) {
                        this.availableSlots.push.apply(this.availableSlots, slots);
                    } else {
                        var errorResponseMessage='';
                        if (typeof response === 'string') {
                            errorResponseMessage = response;
                        } else {
                            errorResponseMessage = "Oops!! Time Slots not Available for selected Date";
                        }
                        globalMessageList.addErrorMessage({
                            message: errorResponseMessage
                        });
                    }
            },
            setOptionDisable: function (option, item) {
                ko.applyBindingsToNode(option, {disable: item.disable}, item);
            },
            isLoggedIn: function () {
                return customer.isLoggedIn();
            },
            updateSlots: function () {
                var serviceUrl;
                if (!customer.isLoggedIn()) {
                    serviceUrl = urlBuilder.createUrl('/deliverySellerSlotConfig/:cartId', {
                        cartId: quote.getQuoteId()
                    });
                } else {
                    serviceUrl = urlBuilder.createUrl('/deliverySellerSlotConfig/mine', {});
                }
                var zipCode = quote.shippingAddress._latestValue.postcode;

                var url = mageurl.build(serviceUrl+'?zip_code='+zipCode+'&target_date='+$('#targetDate').val(), {});
                var obj = this;
                storage.get(
                    url, false
                ).done(function (response) {
                    obj.finalSlots(response);
                    deferred.resolve();
                }).fail(function (response) {
                    //errorProcessor.process(response, messageContainer);
                    deferred.reject();
                });

                // this.request = $.ajax({
                //     url: url,
                //     type: 'get',
                //     dataType: 'json',
                //     context: this,
                //     showLoader: true,
                // }).done(function (response) {
                //     this.finalSlots(response);
                // })
            },

        });
    }
);

