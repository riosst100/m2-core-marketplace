/*global alert*/
define([
    'jquery'
], function ($) {
    'use strict';
    /** Override default place order action and add delivery slot info to request */
    return function (paymentData) {
        /**
         * GET the Delivery Info from Billing Step
         *
         * @template \Lofmp\DeliverySlot\template\deliveryslot\deliveryslot-information
         *
         * @type {*|jQuery}
         */
        var date = $('#targetDate').val();
        var slot = $('#slotName').val();
        var comments = $('#deliveryComments').val();

        // Check the Extension attributes defined or not
        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        // Add Slot data to payment interface request
        paymentData['extension_attributes']['delivery_slot'] = slot;
        paymentData['extension_attributes']['delivery_date'] = date;
        paymentData['extension_attributes']['delivery_comment'] = comments;
    };
});
