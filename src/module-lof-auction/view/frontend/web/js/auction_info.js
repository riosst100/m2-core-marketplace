define([
    'ko',
    'underscore',
    'jquery',
    'jquery-ui-modules/widget',
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'Lof_Auction/js/model/url-builder',
    'mage/url',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'Lof_Auction/js/model/bid-modal',
    'Lof_Auction/js/model/countdown',
    'underscore',
    'validation'
    ], function(
        ko,
        _,
        $,
        jqueyUi,
        utils,
        alert,
        $t,
        urlBuilder,
        mageurl,
        customer,
        storage,
        bidModal,
        countdown
    ) {
        var deferred = $.Deferred();

        $.widget('lof.auctionInfoWidget', {
            options: {
                wrapper: '.lof-auction-product'
            },
            auctionIdList: [],
            auctionIds: [],
            auctionData: {},
            auctionConfig: typeof(window.auctionConfig) !== "undefined" ? window.auctionConfig : {},
            rangePrice: 0,
            isVisible: ko.observable(false),
            isLoading: ko.observable(false),
            countdownObj: null,
            winnerMsg: "",
            step: 0,
            biddings: [],

            _create: function() {
               this._initWidgets();
               this._bindEvents();
            },
            _initWidgets: function() {
                var self = this, conf = this.options;
                if ($(conf.wrapper).length > 0) {
                    $(conf.wrapper).each(function() {
                        var auctionId = $(this).data("auctionid");
                        var productSku = $(this).data("sku");
                        if ($.inArray(auctionId, self.auctionIds) === -1) {
                            self.auctionIds.push(auctionId);
                            self.auctionIdList.push({
                                "id": auctionId,
                                "sku": productSku
                            });
                        }
                    })
                }
            },
            getAuctionFieldValue: function (field_name, auctionKey) {
                var self = this;
                if (!self.auctionData || typeof(self.auctionData[auctionKey]) == "undefined") {
                    return "";
                }
                var field_value = typeof(self.auctionData[auctionKey][field_name]) != "undefined" ? self.auctionData[auctionKey][field_name] : "";
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
            _formatDigitNumber: function(number) {
                return number ? parseFloat(number).toFixed(2) : number
            },
            formatPrice: function (value) {
                return utils.formatPrice(value, this.priceFormat)
            },
            _bindEvents: function() {
                var self = this;
                if (self.auctionIdList.length <= 0) {
                    return;
                }
                $.each(self.auctionIdList, function (index, element) {
                    self._getAuctionInfo(element.id, element.sku);
                })
            },
            _getAuctionInfo: function(auctionId, sku) {
                var self = this
                if (auctionId) {
                    var wrapperId = this.getWrapperId(auctionId);
                    if ($(wrapperId).find(".mp_bidding_form").length) {
                        $(wrapperId).find(".mp_bidding_form").hide();
                    }
                    var serviceUrl = self._getAuctionInfoUrl(sku);
                    $.ajax({
                        url: serviceUrl,
                        dataType: 'json',
                        headers: { 'Content-Type': 'application/json' },
                        data: JSON.stringify({"request": {"sku": sku} }),
                        type:'POST',
                        success: function(data) {
                            self._updateAuctionInfo(auctionId, data);
                            self._getPricerange(auctionId);
                            self._getBidHistory(auctionId);
                            if ($(wrapperId).find(".mp_bidding_form").length) {
                                $(wrapperId).find(".mp_bidding_form").show();
                            }
                            if ($(wrapperId).find(".wait-for-load").length) {
                                $(wrapperId).find(".wait-for-load").show();
                            }
                        }
                    });
                }
            },
            _getBidHistory: function(auctionId) {
                var self = this
                if (auctionId) {
                    var serviceUrl = self._getAuctionBids(auctionId);
                    $.ajax({
                        url: serviceUrl,
                        dataType: 'json',
                        headers: { 'Content-Type': 'application/json' },
                        data: JSON.stringify({"entityId": auctionId, "searchCriteria": {}}),
                        type:'POST',
                        success: function(data) {
                            self._updateBidHistory(auctionId, data);
                        }
                    });
                }
            },
            _getPricerange: function(auctionId) {
                var self = this
                if (auctionId) {
                    var serviceUrl = self._getPriceRangeUrl(auctionId);
                    $.ajax({
                        url: serviceUrl,
                        dataType: 'json',
                        headers: { 'Content-Type': 'application/json' },
                        data: JSON.stringify({"entity_id": auctionId}),
                        type:'POST',
                        success: function(data) {
                            self._updatePriceRange(auctionId, data);
                        }
                    });
                }
            },
            _updateAuctionInfo: function(auctionId, data) {

                var wrapperId = this.getWrapperId(auctionId);
                var auctionKey = this.getAuctionKey(auctionId);
                this.auctionData[this.getAuctionKey(auctionId)] = data

                var customerName = this.getAuctionFieldValue("customer_name", auctionKey);
                var minAmount = this.getAuctionFieldValue("min_amount", auctionKey);
                var newPriceFormat = minAmount ? this.formatPrice(minAmount) : "";
                if ($(wrapperId).find(".bidder-price").length) {
                    $(wrapperId).find(".bidder-price").text(newPriceFormat);
                }
                if ($(wrapperId).find(".bidder-name").length) {
                    $(wrapperId).find(".bidder-name").text(customerName);
                }
                this._runCountdown(auctionId)
            },
            _updateBidHistory: function(auctionId, data) {
                var wrapperId = this.getWrapperId(auctionId);
                var bidHistorySelector = this.getBidHistorySelector(auctionId);
                if ($(wrapperId).find(".number-of-bid").length && typeof(data.total_count) != "undefined") {
                    $(wrapperId).find(".number-of-bid").text(data.total_count + $t(" Bids"));
                }
                if ($(bidHistorySelector).length > 0) {
                    var newHistoryList = this._renderHistoryList(data);
                    $(bidHistorySelector).find("tbody").html(newHistoryList);
                }
            },
            _updatePriceRange: function(auctionId, data) {
                var self = this;
                var newPrice = data ? parseFloat(data) : 0
                var newPriceFormat = data ? this.formatPrice(data) : "";
                var wrapperId = this.getWrapperId(auctionId);
                if ($(wrapperId).find(".range-price").length) {
                    $(wrapperId).find(".range-price").each(function() {
                        var currentValue = 0
                        if ($(this).hasClass("input-text")) {
                            currentValue = $(this).val();
                            if (parseFloat(currentValue) < newPrice) {
                                $(this).val(newPrice)
                            }
                        } else {
                            currentValue = $(this).text();
                            if (parseFloat(currentValue) < newPrice) {
                                $(this).text(newPrice)
                            }
                        }
                    })
                }
                if ($(wrapperId).find(".range-price-format").length) {
                    $(wrapperId).find(".range-price-format").each(function() {
                        var currentValue = $(this).data("price")
                        if (parseFloat(currentValue) < newPrice) {
                            $(this).data("price", newPrice);
                            $(this).html(newPriceFormat);
                        }
                    })
                }
            },
            _runCountdown: function(auctionId) {
                var wrapperId = this.getWrapperId(auctionId);
                var auctionKey = this.getAuctionKey(auctionId);
                var stopTime = this.getAuctionFieldValue("stop_auction_utc_time", auctionKey);
                var startTime = this.getAuctionFieldValue("start_auction_utc_time", auctionKey);
                if ($(wrapperId).length) {
                    $(wrapperId).each(function(index) {
                        var countdownElement = $(this).find(".count-down-wrapper");
                        var countdownFieldId = $(countdownElement).length ? $(countdownElement).attr("id") : "";
                        if (countdownFieldId && stopTime) {
                            $("#"+countdownFieldId).data("datetime", stopTime)
                            $("#"+countdownFieldId).data("starttime", startTime)
                        }
                    });
                }
            },
            _getAuctionInfoUrl: function(sku) {
                var serviceUrl = urlBuilder.createUrl('/getAuctionInformation/' + sku, {});
                return mageurl.build(serviceUrl, {});
            },
            _getPriceRangeUrl: function(auctionId) {
                var serviceUrl = urlBuilder.createUrl('/getMinAuctionAmount/' + auctionId, {});
                return mageurl.build(serviceUrl, {});
            },
            _getAuctionBids: function(auctionId) {
                var serviceUrl = urlBuilder.createUrl('/getAuctionBids/' + auctionId, {});
                return mageurl.build(serviceUrl, {});
            },
            getWrapperId: function(auctionId) {
                return ".auction-item-" + auctionId;
            },
            getAuctionKey: function(auctionId) {
                return "auction" + auctionId;
            },
            getBidHistorySelector: function(auctionId) {
                return ".bid-history-table-" + auctionId;
            },
            _renderHistoryList: function(data) {
                var self = this;
                var show_auto_bid_amount = this.getAuctionConfigValue("show_auto_bid_amount");
                var show_price = this.getAuctionConfigValue("show_price");
                var show_bidder = this.getAuctionConfigValue("show_bidder");
                var html = "";
                if (typeof(data.items) != "undefined") {
                    $.each(data.items, function (index, item) {
                        var itemPrice = bidderName = '******';
                        if ((item.is_auto && show_auto_bid_amount) || (!item.is_auto && show_price)) {
                            itemPrice = self.formatPrice(item.amount);
                        }
                        if (show_bidder) {
                            bidderName = item.customer_name;
                        }

                        html += "<tr>";
                        html += '<td class="col auc-history-bidder">'+bidderName+'</td>';
                        html += '<td class="col auc-history-date">'+item.created_at+'</td>';
                        html += '<td class="col auc-history-amount"><span class="price">'+itemPrice+'</span></td>';
                        html += "</tr>";
                    });

                }
                return html;
            }
        });
        return $.lof.auctionInfoWidget;
});
