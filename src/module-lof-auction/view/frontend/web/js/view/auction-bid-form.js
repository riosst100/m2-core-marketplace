define([
    'uiComponent',
    'ko',
    'underscore',
    'jquery',
    'mage/storage',
    'mage/translate',
    'Magento_Catalog/js/price-utils',
    'Lof_Auction/js/model/url-builder',
    'mage/url',
    'Lof_Auction/js/model/auction-winner',
    'Lof_Auction/js/model/countdown',
    'Lof_Auction/js/model/bid-modal',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/customer-data'
], function (Component, ko, _, $, storage, $t, utils, urlBuilder, mageurl, auctionWinner, countdown, bidModal, customer, customerData) {
    'use strict';

    var deferred = $.Deferred();

    return Component.extend({
        biddings: [],
        winner: {},
        wattingUser: {},
        customer: null,
        sku: "",
        storeCode: 'default',
        auctionData: null,
        priceFormat: typeof(window.PriceFormat) !== "undefined" ? window.PriceFormat : {"pattern":"$%s","precision":2,"requiredPrecision":2,"decimalSymbol":".","groupSymbol":",","groupLength":3,"integerRequired":false},
        auctionStatus: '', //processing, win, expired
        auctionConfig: typeof(window.auctionConfig) !== "undefined" ? window.auctionConfig : {},
        auctionAfterComplete: null,
        rangePrice: 0,
        isVisible: ko.observable(false),
        isLoading: ko.observable(false),
        countdownObj: null,
        defaults: {
            template: 'Lof_Auction/auction-bid-form'
        },
        /**
         * Initializes related component.
         */
        initialize: function () {
            this._super();

            this.customer = customerData.get('customer');
            return this;
        },

        isLoadedCompleted: function () {
            return this.isLoaded
        },

        callApi: function(url, body, callback) {
            this.isLoading(true);
            storage.post(
                url,
                body && typeof(body) == 'object' ? JSON.stringify(body) : "{}",
                false
            ).done(function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
                deferred.resolve();
            }).fail(function (response) {
                deferred.reject();
            }).always(function () {
                this.isLoading(false);
            }.bind(this));
        },
        setSku: function (sku) {
            this.sku = sku
            return this
        },
        formatPrice: function (value) {
            return utils.formatPrice(value, this.priceFormat)
        },
        getAuctionProductId: function () {
            return this.getAuctionFieldValue("entity_id");
        },
        getAuctionFieldValue: function (field_name, parent) {
            var self = this;
            if (!parent.auctionData) {
                return "";
            }
            var field_value = typeof(parent.auctionData[field_name]) != "undefined" ? parent.auctionData[field_name] : "";
            return field_value;
        },
        isAutoEnable: function() {
            return parseInt(this.getAuctionConfigValue("auto_enable"));
        },
        getAuctionConfigValue: function (path) {
            var self = this;
            if (!self.auctionConfig) {
                return "";
            }
            var field_value = typeof(self.auctionConfig[path]) != "undefined" ? self.auctionConfig[path] : "";
            return field_value;
        },
        isExpired: function () {
            return (this.auctionStatus == 'expired')
        },
        isWin: function () {
            return (this.auctionStatus == 'win')
        },
        isProcessing: function (parent) {
            var current_time_stamp = this.getAuctionFieldValue('current_time_stamp', parent);
            var stop_auction_time_stamp = this.getAuctionFieldValue('stop_auction_time_stamp', parent);
            return parseFloat(current_time_stamp) <= parseFloat(stop_auction_time_stamp);
        },
        isActive: function () {
            var flag = (typeof this.auctionConfig !== 'undefined') && this.auctionConfig.enable && this.auctionData ? true : false;
            return flag;
        },
        isLoggedIn: function () {
            return customer.isLoggedIn();
        },
        getCustomerData: function () {
            return this.customer;
        },
        getCountdownId: function (parent) {
            return "count-down-auction-"+this.getAuctionFieldValue('entity_id', parent)+""+this.auctionKey
        },
        getBidAmountId: function (parent) {
            return "bidding_amount"+this.getAuctionFieldValue('entity_id', parent)+""+this.auctionKey
        },
        getBtnSubmitId: function (parent) {
            return "btn-submit"+this.getAuctionFieldValue('entity_id', parent)+""+this.auctionKey
        },
        getAuctionBidUrl: function (parent) {
            return window.BASE_URL+"lofauction/account/login/id/"+this.getAuctionFieldValue('entity_id', parent)
        },
        getAuctionDataStr() {
            var auctionDataStr = "{}";
            if (self.auctionData) {
                auctionDataStr = JSON.stringify(self.auctionData);
            }
            return auctionDataStr;
        },
        getAuctionTitle: function (parent) {
            return $t("Bid on ") + this.getAuctionFieldValue('auction_title', parent);
        },
        getBidNote: function (parent) {
            return $t('The added price must be a multiple of %1.').replace('%1', parent.step);
        },
        isDisableManual: function () {
            var disableManual = this.getAuctionConfigValue('disable_manual');
            return parseInt(disableManual) == 0 ? false : true;
        },
        getRangePrice: function (parent) {
            var minAmount = this.getAuctionFieldValue('starting_price', parent);
            var rangePrice = parent.newRangePrice
            return rangePrice ? rangePrice : minAmount
        }
    });
});
