/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/validation'
], function ($) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        deliverySlotConfig = checkoutConfig ? checkoutConfig.delivery_slot : {};
        //agreementsInputPath = '.payment-method._active div.checkout-agreements input';

    return {
        /**
         * Validate delivery Slot Fields
         *
         * @returns {Boolean}
         */
        validate: function () {
            var isValid = true;

            if (!deliverySlotConfig.enable) {
                return true;
            }
            var dateElement = $('#targetDate');
            if (!($.validator.validateSingleElement(dateElement, {
                    errorElement: 'div'
                }))) {
                isValid = false;
            }
           
            return isValid;
        }
    };
});
