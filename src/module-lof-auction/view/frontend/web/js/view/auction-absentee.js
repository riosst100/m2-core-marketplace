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
        auctionId: 0,
        auctionKey: "",
        wrapper: "",
        isVisible: ko.observable(false),
        isLoading: ko.observable(false),
        countdownObj: null,
        defaults: {
            template: 'Lof_Auction/auction-absentee'
        },
        /**
         * Initializes related component.
         */
        initialize: function () {
            this._super();
            this.customer = customerData.get('customer');

            this.wrapper = "#"+this.getAbsenteeId();
            this.getUrl = this.getAbsenteeGetUrl();

            return this;
        },

        isLoadedCompleted: function () {
            return this.isLoaded
        },

        initFieldElements: function () {
            if (typeof(this.$auctionAbsenteeWrapper) == "undefined") {
                this.$auctionAbsenteeWrapper = $(this.wrapper);
            }
            if (typeof(this.$auctionAbsenteeForm) == "undefined") {
                this.$auctionAbsenteeForm = $(this.wrapper).find("form");
            }
            if (typeof(this.$absenteeInput) == "undefined") {
                this.$absenteeInput = $(this.wrapper).find(".max-absentee-bid-input");
            }
            if (typeof(this.$submitButton) == "undefined") {
                this.$submitButton = $(this.wrapper).find(".btn-place-max-bid");
            }
            if (typeof(this.$maxBidAmountLabel) == "undefined") {
                this.$maxBidAmountLabel = $(this.wrapper).find(".max-bid-amount");
            }
            if (typeof(this.$maxBidAmountPrice) == "undefined") {
                this.$maxBidAmountPrice = $(this.wrapper).find(".max-bid-price > .price");
            }
            if (typeof(this.$inputBidField) == "undefined") {
                this.$inputBidField = $(this.wrapper).parent().find(".mpbidding_amount");
            }

            return this;
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

        _formatDigitNumber: function(number) {
            return number ? parseFloat(number).toFixed(2) : number
        },

        _bindEvents: function() {
            var self = this
            if (this.$submitButton && this.$submitButton.length) {
                this.$submitButton.on("click", function() {
                    this._updateMaxAbsenteeBid();
                })
            }
            if (this.$absenteeInput && this.$absenteeInput.length) {
                this.$absenteeInput.on("change", function() {
                    if (self.$maxBidAmountLabel && self.$maxBidAmountLabel.length) {
                        self.$maxBidAmountLabel.text($(this).val())
                    }
                    if (self.$maxBidAmountPrice && self.$maxBidAmountPrice.length) {
                        self.$maxBidAmountPrice.text(self.formatPrice($(this).val()))
                    }
                })
            }
        },
        _getCurrenMaxAbstenteBid: function() {
            var self = this
            this.isLoading(true);
            $.ajax({
                url: this.getUrl,
                dataType: 'json',
                data: {"auction_id": this.auctionId},
                type:'POST',
                success: function(data) {

                    self.initFieldElements();
                    self._bindEvents();

                    if (self.$absenteeInput && self.$absenteeInput.length > 0 && data && typeof(data.max_absentee_amount) != "undefined") {
                        var current_min_bid_amount = (typeof (self.$inputBidField) !== "undefined" && self.$inputBidField && self.$inputBidField.length > 0 ) ? self.$inputBidField.val() : data.max_absentee_amount;

                        current_min_bid_amount = parseFloat(current_min_bid_amount) > parseFloat(data.max_absentee_amount) ? current_min_bid_amount :  data.max_absentee_amount;

                        self.$absenteeInput.val(self._formatDigitNumber(current_min_bid_amount));
                        self.$absenteeInput.change();
                    }
                    deferred.resolve();
                    self.isLoading(false);
                }
            });
            return true;
        },
        _updateMaxAbsenteeBid: function() {
            var self = this;
            var data = {};
            if (!self.$auctionAbsenteeForm.valid()) {
                return false;
            }

            data.auctionId =  this.auctionId;
            data.maxBidAmount = this.$absenteeInput.val();

            self.$auctionAbsenteeForm.submit();
        },

        setSku: function (sku) {
            this.sku = sku
            return this
        },

        formatPrice: function (value) {
            return utils.formatPrice(value, this.priceFormat)
        },

        isEnableAbsentee: function () {
            return true
        },

        getAuctionFieldValue: function (field_name, parent) {
            var self = this;
            if (!parent.auctionData) {
                return "";
            }
            var field_value = typeof(parent.auctionData[field_name]) != "undefined" ? parent.auctionData[field_name] : "";
            return field_value;
        },

        getAuctionConfigValue: function (path) {
            var self = this;
            if (!self.auctionConfig) {
                return "";
            }
            var field_value = typeof(self.auctionConfig[path]) != "undefined" ? self.auctionConfig[path] : "";
            return field_value;
        },

        showProductName: function () {
            var startingTime = this.getStartingTime();
            return startingTime ? true : false;
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
            this.auctionId = this.getAuctionFieldValue('entity_id', parent)
            return "count-down-auction-"+this.getAuctionFieldValue('entity_id', parent)+""+this.auctionKey
        },
        getBidAmountId: function (parent) {
            this.auctionId = this.getAuctionFieldValue('entity_id', parent)
            return "bidding_amount"+this.getAuctionFieldValue('entity_id', parent)+""+this.auctionKey
        },
        getBtnSubmitId: function (parent) {
            this.auctionId = this.getAuctionFieldValue('entity_id', parent)
            return "btn-submit"+this.getAuctionFieldValue('entity_id', parent)+""+this.auctionKey
        },
        getAbsenteeSetUrl: function () {
            return window.BASE_URL+"lofauction/account/setMaxabsentee/"
        },
        getAbsenteeGetUrl: function () {
            return window.BASE_URL+"lofauction/account/getMaxabsentee/"
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
        getAbsenteeBidInputClass: function (parent) {
            var minAmount = this.getAuctionFieldValue('starting_price', parent);
            var maxAmount = this.getAuctionFieldValue('max_amount', parent);
            return "input-text field-input max-absentee-bid-input required-entry validate-digits-range digits-range-" + minAmount + "-" + maxAmount;
        },
        getRangePrice: function (parent) {
            var minAmount = this.getAuctionFieldValue('starting_price', parent);
            var rangePrice = parent.newRangePrice
            return rangePrice ? rangePrice : minAmount
        },
        getAbsenteeId: function() {
            return "max_absentee_bid_wrapper"+this.auctionId + "_" + this.auctionKey;
        }
    });
});
