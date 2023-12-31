define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Lofmp_DeliverySlot/js/model/delivery-slot-validator'
], function (Component, additionalValidators, deliverySlotValidator) {
    'use strict';

    additionalValidators.registerValidator(deliverySlotValidator);

    return Component.extend({});
});