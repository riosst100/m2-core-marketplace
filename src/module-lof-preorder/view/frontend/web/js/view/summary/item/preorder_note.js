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
    'Magento_Checkout/js/view/summary/abstract-total'
], function (viewModel) {
    'use strict';
    var quotePreorderMessages = window.checkoutConfig.quotePreorderMessages;
    return viewModel.extend({
        defaults: {
            displayArea: 'after_details',
            template: 'Lof_PreOrder/summary/item/details/preorder_note'
        },
        quotePreorderMessages: quotePreorderMessages,

        /**
         * @param {Object} quoteItem
         * @return {*|String}
         */
        getValue: function (quoteItem) {
            if (typeof(this.quotePreorderMessages) != "undefined" && typeof(quoteItem['item_id']) !="undefined" && typeof(this.quotePreorderMessages[quoteItem['item_id']]) != 'undefined') {
                return this.quotePreorderMessages[quoteItem['item_id']];
            }

            return null;
        }
    });
});
