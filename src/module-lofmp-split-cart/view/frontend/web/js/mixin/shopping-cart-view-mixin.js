/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (targetWidget) {
        $.widget('mage.shoppingCart', targetWidget, {
            /**
             * Prepares the form and submit to clear the cart
             * @public
             */
            clearCart: function () {
                $(this.options.emptyCartButton).attr('name', 'update_cart_action_temp');
                $(this.options.updateCartActionContainer)
                    .attr('name', 'update_cart_action').attr('value', 'empty_cart_split');

                if ($(this.options.emptyCartButton).parents('form').length > 0) {
                    $(this.options.emptyCartButton).parents('form').submit();
                }
            }
        });

        return $.mage.shoppingCart;
    };
});
