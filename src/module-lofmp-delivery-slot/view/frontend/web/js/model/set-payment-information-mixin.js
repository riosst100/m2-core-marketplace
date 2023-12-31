define([
    'jquery',
    'mage/utils/wrapper',
    'Lofmp_DeliverySlot/js/model/deliveryslot-assigner'
], function ($, wrapper, deliveryslotAssigner) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place order action and add delivery slot info to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            deliveryslotAssigner(paymentData);

            return originalAction(paymentData, messageContainer);
        });
    };
});
