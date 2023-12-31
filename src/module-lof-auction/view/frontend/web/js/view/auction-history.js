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
        auctionId: 0,
        sku: "",
        productId: 0,
        storeCode: 'default',
        priceFormat: typeof(window.PriceFormat) !== "undefined" ? window.PriceFormat : {"pattern":"$%s","precision":2,"requiredPrecision":2,"decimalSymbol":".","groupSymbol":",","groupLength":3,"integerRequired":false},
        auctionStatus: '', //processing, win, expired
        auctionConfig: typeof(window.auctionConfig) !== "undefined" ? window.auctionConfig : {},
        auctionAfterComplete: null,
        rangePrice: 0,
        auctionKey: "",
        numberOfBid: 0,
        loaddedBids: false,
        isVisible: ko.observable(false),
        isLoading: ko.observable(false),
        defaults: {
            template: 'Lof_Auction/auction-history'
        },
        /**
         * Initializes related component.
         */
        initialize: function () {
            this._super();
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

        formatPrice: function (value) {
            return utils.formatPrice(value, this.priceFormat)
        },

        getAuctionConfigValue: function (path) {
            var self = this;
            if (!self.auctionConfig) {
                return "";
            }
            var field_value = typeof(self.auctionConfig[path]) != "undefined" ? self.auctionConfig[path] : "";
            return field_value;
        },
        getAbsenteeId: function() {
            return "lofProductAuction" + this.auctionKey;
        },
        listBids: function () {
            var self = this;
            if (this.auctionId && !this.loaddedBids) {

                var auctionBidsCreateUrl = urlBuilder.createUrl('/getAuctionBids/'+this.auctionId, {});
                var auctionBidsUrl = mageurl.build(auctionBidsCreateUrl, {});

                self.callApi(auctionBidsUrl, { 'entityId': this.auctionId, 'searchCriteria': {} }, function (response) {
                    self.$numberBidField = $("#"+self.getAbsenteeId()).find(".number_of_bids");
                    self.$listBidsField = $("#"+self.getAbsenteeId()).find(".list-bids");

                    if (response && typeof(response.items) != "undefined") {
                        self.biddings = response.items;
                        $(self.$numberBidField).text(response.total_count + $t(" Bid"));
                        self._renderBidding(self.biddings);
                        self._initModalBox();
                    }
                })
                self.loaddedBids = true;
            }
            return this;
        },
        getAbsenteeSetUrl: function () {
            return window.BASE_URL+"lofauction/account/setMaxabsentee/"
        },
        getHistoryId: function () {
            return "lof_history_bid" + this.auctionId + "_" + this.auctionKey
        },
        getBidLinkId: function () {
            return "bid_link" + this.auctionId + "_" + this.auctionKey
        },
        getNumberOfBid: function () {
            this.listBids();
            return this.numberOfBid + $t(" Bid");
        },
        _initModalBox: function () {
            bidModal.initModal("#" + this.getBidLinkId(), "#" + this.getHistoryId());
            return this;
        },
        _renderBidding: function (biddings) {
            var self = this;
            if (biddings.length > 0) {
                var show_price = this.getAuctionConfigValue("show_price");
                var show_bidder = this.getAuctionConfigValue("show_bidder");
                $.each(biddings, function (index, value) {
                    var trElement = $('<tr></tr>');
                    var amountElement = $('<td class="col auc-history-amount"></td>');
                    var dateElement = $('<td class="col auc-history-date"></td>');
                    var bidderElement = $('<td class="col auc-history-bidder"></td>');
                    var bidAmount = '****';
                    var bidName = '****';
                    if (show_price) {
                        bidAmount = self.formatPrice(value.amount);
                    }
                    if (show_bidder) {
                        bidName = value.customer_name;
                    }
                    amountElement.text(bidAmount);
                    bidderElement.text(bidName);
                    dateElement.text(value.utc_created_at);

                    trElement.append(bidderElement);
                    trElement.append(dateElement);
                    trElement.append(amountElement);
                    self.$listBidsField.append(trElement);
                });
            }
        }
    });
});
