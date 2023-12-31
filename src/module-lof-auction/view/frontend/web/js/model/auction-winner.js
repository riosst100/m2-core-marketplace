/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function ($, utils, $t) {
    'use strict';

    return {
        auctionData: null,
        priceFormat: typeof(window.PriceFormat) !== "undefined" ? window.PriceFormat : {"pattern":"$%s","precision":2,"requiredPrecision":2,"decimalSymbol":".","groupSymbol":",","groupLength":3,"integerRequired":false},
        allowForBuy: 0,
        rangePrice: 0,
        winnerPrice: 0,
        addtocartButton: '#product-addtocart-button',
        addtocartForm: '#product_addtocart_form',

        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        initWinner: function (auctionData, allowForBuy, rangePrice, winnerPrice) {
            var self = this;
            self.auctionData = auctionData
            self.allowForBuy = allowForBuy
            self.rangePrice = rangePrice
            self.winnerPrice = winnerPrice
            if (allowForBuy && winnerPrice) {
                var bidWinnerCart = utils.formatPrice(winnerPrice, self.priceFormat, false);
                var btnText = $t("Buy with ") + bidWinnerCart;
                $(self.addtocartButton + ' span').text(btnText);
            } else if (auctionData && typeof(auctionData['pro_buy_it_now']) != undefined && auctionData['pro_buy_it_now']) {
                $(self.addtocartButton + ' span').text($t('Buy It Now'));
            } else if (self.getStartingTime()) {
                $(self.addtocartForm).remove();
            }

            $("input[name='auto_bid_allowed']").on('change', function () {
                if ($(this).is(":checked")) {
                    $("#bidding_amount").val(rangePrice);
                    $("#bidding_amount").prop('disabled', true);
                } else {
                    $("#bidding_amount").prop('disabled', false);
                }
            })
        },
        getStartingTime: function () {
            var self = this;
            if (!self.auctionData) {
                return 0;
            }
            var starting = parseFloat(self.auctionData['start_auction_time_stamp']) <= parseFloat(self.auctionData['current_time_stamp']);
            return starting;
        }
    };
});
