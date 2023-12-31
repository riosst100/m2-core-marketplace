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
    //'Lof_Auction/js/model/bid-modal',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/customer-data'
], function (Component, ko, _, $, storage, $t, utils, urlBuilder, mageurl, auctionWinner, countdown, customer, customerData) {
    'use strict';

    var newRangePrice = 0;
    var currentCustomer = customerData.get('customer');
    var deferred = $.Deferred();

    return Component.extend({
        auctionKey: "",
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
        expireFullInfo: 1,
        isVisible: ko.observable(false),
        isLoading: ko.observable(false),
        showProductName : ko.observable(false),
        countdownObj: null,
        winnerMsg: "",
        step: 0,
        defaults: {
            template: 'Lof_Auction/activeAuction'
        },
        /**
         * Initializes related component.
         */
        initialize: function () {
            this._super();
            this.sku = this.auctionData ? this.auctionData['sku'] : '';
            this.initData();

            return this;
        },

        initData: function() {
            //init auction data, check status is
            this.getAuctionData(this.sku);
            //this.runCountdown();
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
                ko.options.deferUpdates = true;
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

        getAuctionData: function (sku) {
            var self = this;
            var serviceUrl = urlBuilder.createUrl('/getAuctionInformation/'+sku, {});
            var url = mageurl.build(serviceUrl, {});
            this.isLoading(true);

            return storage.post(
                url,
                JSON.stringify({"request": {"sku" : sku} }),
                false
            ).done(function (response) {
                if (typeof response != 'object') {
                    return false;
                }
                if (typeof(response.product_id) != undefined && response.product_id) {
                    if (typeof(response.data_array) != undefined) {
                        delete response.data_array;
                    }
                    self.auctionData = response;
                    if (typeof(response['buy_it_now']) != "undefined") {
                        self.auctionData['pro_buy_it_now'] = parseInt(response['buy_it_now'])
                    }
                    self.initWinner();
                    self.getRangePrice();
                } else {
                    console.log("Can not get auction data for sku: " + sku)
                }
                deferred.resolve();
                self.isVisible(true);
                ko.options.deferUpdates = true;
                self.checkShowProductName();
                return response
            }).fail(function (response) {
                deferred.reject();
                self.isVisible(true);
            }).always(function () {
                this.isLoading(false);
                this.isVisible(true);
            }.bind(this));
        },

        getRangePrice: function () {
            var self = this;
            if (this.auctionData && !this.rangePrice) {
                var auctionId = this.getAuctionFieldValue('entity_id');
                var minAuctionAmountUrl = urlBuilder.createUrl('/getMinAuctionAmount/'+auctionId, {});
                var minAmountUrl = mageurl.build(minAuctionAmountUrl, {});

                ko.computed(function() {
                    this.callApi(minAmountUrl, { "entity_id": auctionId }, function (response) {
                        self.rangePrice = response ? parseFloat(response.replace(/,/g, '')) : 0
                        self.updateAuctionFields();
                    })
                }, this)
            }
            return this.rangePrice
        },

        initWinner: function () {
            var self = this;
            if (this.auctionData) {
                var isProcessing = this.isProcessing();
                var auctionId = this.getAuctionFieldValue('entity_id');
                var auctionDetailAfterEnd = urlBuilder.createUrl('/getAuctionDetailAfterEnd/'+auctionId, {});
                var auctionDetailAfterEndUrl = mageurl.build(auctionDetailAfterEnd, {});

                if (!isProcessing) {
                    if (self.isLoggedIn()) {
                        ko.computed(function() {
                            self.callApi(auctionDetailAfterEndUrl,
                            {
                                'entityId': auctionId,
                                'productId' : self.getAuctionFieldValue('product_id')
                            }, function (response) {
                                self.auctionAfterComplete = response
                                if (typeof (response["winner"]) != "undefined") {
                                    self.winner = response["winner"];
                                }
                                if (typeof (response["watting_user"]) != "undefined") {
                                    self.wattingUser = response["watting_user"];
                                }

                                if ( self.winner && typeof(self.winner['price']) != 'undefined' && parseFloat(self.winner['price']) > 0) {
                                    if (parseInt(self.winner['time_for_buy']) > 0 && parseInt(self.winner['shop']) == 0) {
                                        self.winnerMsg = self.winnerMsg + " "+ $t("Now, you can get it in") + " " + self.formatPrice(self.winner['price']);
                                        auctionWinner.initWinner(self.auctionData, 1, self.rangePrice, self.winner['price']);
                                    }
                                }
                                
                                self.showWinnerInformation();
                            })
                        }, this)
                    } else {
                        auctionWinner.initWinner(self.auctionData, 0, self.rangePrice, self.getAuctionFieldValue('reserve_price'));
                    }

                } else {
                    auctionWinner.initWinner(self.auctionData, 0, self.rangePrice, self.getAuctionFieldValue('reserve_price'));
                }
            }
        },

        updateAuctionFields: function () {
            var self = this;
            var wrapperId = this.getWrapperId();
            var listFields = [
                "auction_title",
                "start_auction_time",
                "stop_auction_time",
                "time_zone",
                "today_time",
                "min_qty",
                "max_qty",
                "starting_price",
                "max_amount",
                "reserve_price",
                "min_amount",
                "customer_name",
                "range_price"
            ];

            $(wrapperId).hide();
            if (!this.isExpired() && this.showProductName()) {
                if (!this.isProcessing()) {
                    $(wrapperId + " .bidding-form").remove();
                    $(wrapperId + " .hide-when-done").remove();
                } else {
                    listFields.forEach((fieldName, index) => {
                        var newValue = "";
                        switch (fieldName) {
                            case "auction_title":
                                newValue = this.getAuctionTitle();
                            break;
                            case "range_price":
                                newValue = self.rangePrice;
                            break;
                            default:
                                newValue = self.getAuctionFieldValue(fieldName)
                            break;
                        }
                        var fieldSelectors = $(wrapperId + " ."+fieldName)
                        if (fieldSelectors && fieldSelectors.length > 0) {
                            $.each(fieldSelectors, function (index, value) {
                                var fieldValue = newValue
                                if ($(this).hasClass("format-price")) {
                                    fieldValue = self.formatPrice(fieldValue)
                                    $(this).html(fieldValue);
                                } else if ($(this).hasClass("field-input")) {
                                    $(this).val(fieldValue);
                                } else {
                                    $(this).html(fieldValue);
                                }
                            })
                        }
                    })
                    this.runCountdown();
                }
                $(wrapperId).parent().find(".hide-when-comming").show();
                $(wrapperId).show();
            } else if(!this.showProductName()) {
                this.runCountdown();
                $(wrapperId).parent().find(".hide-when-comming").remove();
                $(wrapperId).show();
            } else {
                $(wrapperId).parent().find(".auction-was-expired").show();
            }

            $(wrapperId + " .hidden").removeClass("hidden");
            if ($("#product_addtocart_form").length > 0) {
                $("#product_addtocart_form").show();
            }
            if ($(wrapperId + " .loading-text").length > 0) {
                $(wrapperId + " .loading-text").hide();
            }
            if ($(wrapperId + " .mp_bidding_form").length > 0) {
                $(wrapperId + " .mp_bidding_form").show();
            }
        },

        runCountdown: function () {
            var countdownFieldId = this.getCountdownId();
            var stopTime = this.getAuctionFieldValue("stop_auction_utc_time");
            var startTime = this.getAuctionFieldValue("start_auction_utc_time");
            var confirmEnable = this.getAuctionConfigValue("show_confirm_bid");
            var confirmMessage = this.getAuctionConfigValue("confirm_message");
            if (stopTime) {
                countdown.addPropValue("countdown", "#"+countdownFieldId)
                    .addPropValue("wrapper_selector", this.getWrapperId())
                    .runCountdown(stopTime, startTime, confirmEnable, confirmMessage);
            }
        },

        showWinnerInformation: function () {
            var self = this;
            if ($(this.getWrapperId()).length > 0) {
                if ( self.winner && typeof(self.winner['price']) != 'undefined' && parseFloat(self.winner['price']) > 0) {
                    $(this.getWrapperId()).find(".auction-winner-msg .bid_title").html(this.winnerMsg);
                    $(this.getWrapperId()).find(".show-when-done").show();
                } else if (self.wattingUser && typeof(self.wattingUser['msg_lable']) != 'undefined') {
                    var wattingUser = $('<div class="lof_product_background"></div>');
                    wattingUser.html('<div class="product-collateral"><div class="box-collateral box-tags lof_row"><h3>'+self.wattingUser['msg_lable']+'</h3></div></div>');
                    $(this.getWrapperId()).append(wattingUser)
                }
            }
        },

        isEnableAbsentee: function () {
            return true
        },

        getAuctionFieldValue: function (field_name) {
            var self = this;
            if (!self.auctionData) {
                return "";
            }
            var field_value = typeof(self.auctionData[field_name]) != "undefined" ? self.auctionData[field_name] : "";
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

        checkShowProductName: function () {
            var isStartingTime = this.getStartingTime();
            this.showProductName(isStartingTime);
        },

        getStartingTime: function () {
            var self = this;
            if (!self.auctionData) {
                return 0;
            }
            var starting = parseFloat(self.auctionData['start_auction_time_stamp']) <= parseFloat(self.auctionData['current_time_stamp']);
            return starting;
        },

        isExpired: function () {
            var new_auction_start = this.getAuctionFieldValue('new_auction_start');
            return !new_auction_start && new_auction_start != 'false' && new_auction_start != false ? true : false;
        },

        isWin: function () {
            return (this.auctionStatus == 'win')
        },

        isProcessing: function () {
            var current_time_stamp = this.getAuctionFieldValue('current_time_stamp');
            var stop_auction_time_stamp = this.getAuctionFieldValue('stop_auction_time_stamp');
            return parseFloat(current_time_stamp) <= parseFloat(stop_auction_time_stamp);
        },

        isActive: function () {
            var flag = (typeof this.auctionConfig !== 'undefined') && this.auctionConfig.enable && this.auctionData ? true : false;
            return flag;
        },
        isLoggedIn: function () {
            return !currentCustomer().firstname ? false : true;
        },
        getCustomerData: function () {
            return currentCustomer();
        },
        getWrapperId: function() {
            return "#lofProductAuction" + this.auctionKey;
        },
        getCountdownId: function () {
            return "count-down-auction-"+this.getAuctionFieldValue('entity_id')+""+this.auctionKey
        },
        getBidAmountId: function () {
            return "bidding_amount"+this.getAuctionFieldValue('entity_id')+""+this.auctionKey
        },
        getBtnSubmitId: function () {
            return "btn-submit"+this.getAuctionFieldValue('entity_id')+""+this.auctionKey
        },
        getAuctionBidUrl: function () {
            return window.BASE_URL+"lofauction/account/login/id/"+this.getAuctionFieldValue('entity_id')
        },
        getAuctionDataStr() {
            var auctionDataStr = "{}";
            if (self.auctionData) {
                auctionDataStr = JSON.stringify(self.auctionData);
            }
            return auctionDataStr;
        },
        getAuctionTitle: function () {
            return $t("Bid On ") + this.getAuctionFieldValue('pro_name');
        },
        getBidFormClass: function () {
            return "list-group-item hide-when-done bidding-form hide-when-comming"
        },
        getAuctionBidClass: function () {
            return "auction-bid-form" + (this.expireFullInfo ? ' show-full-info' : ' hide-information');
        }
    });
});
