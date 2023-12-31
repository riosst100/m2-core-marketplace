/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define([
    'jquery',
    'moment',
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, moment, priceUtils, confirmation, $t) {
    'use strict';

    var x = null;

    return {
        countDownField: "#count-down-auction",
        countDownBidTitle: ".starting_soon_bid_title",
        btnSubmit: '#btn-submit',
        biddingAmount: '#bidding_amount',
        auctionAmount: '#auction_amount',
        wrapperSelector: '.auction-product-info',
        priceFormat: typeof(window.PriceFormat) !== "undefined" ? window.PriceFormat : {"pattern":"$%s","precision":2,"requiredPrecision":2,"decimalSymbol":".","groupSymbol":",","groupLength":3,"integerRequired":false},

        addPropValue: function (fieldName, fieldValue) {
            if (fieldName && fieldValue) {
                switch (fieldName) {
                    case "countdown":
                        this.countDownField = fieldValue
                    break;
                    case "bid_title":
                        this.countDownBidTitle = fieldValue
                    break;
                    case "btn_submit":
                        this.btnSubmit = fieldValue
                    break;
                    case "bidding_amount":
                        this.biddingAmount = fieldValue
                    case "wrapper_selector":
                        this.wrapperSelector = fieldValue
                    break;
                    case "auction_amount":
                        this.auctionAmount = fieldValue
                    break;
                    default:

                    break;
                }
            }
            return this;
        },
        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        runCountdown: function (stopTime, startTime, confirmEnable, confirmMessage) {
            var self = this;
            var countDownDate = moment(stopTime);
            var startTime = moment(startTime);
            if (confirmEnable) {
                $(self.btnSubmit).click(function (e) {
                    e.preventDefault();
                    var $el = $(this);
                    var $form = $el.closest('form');
                    var amount = $(self.biddingAmount).val();
                    if (!amount) {
                        amount = $(self.auctionAmount).val();
                    }
                    confirmMessage = confirmMessage.replace('%price%', priceUtils.formatPrice(amount, self.priceFormat, false));
                    confirmation({
                        title: $.mage.__('Are you sure?'),
                        content: $.mage.__(confirmMessage),
                        actions: {
                            confirm: function () {
                                $el.attr("disabled", true);
                                $form.submit()
                            }
                        }
                    })
                })
            }

            x = setInterval(function () {
                var d = new Date();
                var utc = d.getTime() + (d.getTimezoneOffset() * 60000);
                var now = moment(utc);
                var diff = countDownDate.diff(now);
                var diffStart = startTime.diff(now);
                if (diff <= 0) {
                    clearInterval(x);
                    $(self.countDownField).html($t("EXPIRED"));
                    self.stopAuction();
                } else if (diffStart < 0) {
                    let duration = moment.duration(diff);
                    self.countDown(duration);
                    var $title = $(self.countDownBidTitle);
                    $title.text($t("This auction is starting, refresh page to see more information."));
                } else {
                    let duration = moment.duration(diffStart);
                    self.countDown(duration);
                }
            }, 1000);

            return this;
        },
        stopAuction: function() {
            if ($(this.wrapperSelector).find(".bidding-form").length > 0) {
                $(this.wrapperSelector).find(".bidding-form").remove();
            }
            if ($(this.wrapperSelector).find(".max-absentee-bid-wrapper").length > 0) {
                $(this.wrapperSelector).find(".max-absentee-bid-wrapper").remove();
            }
        },
        countDown: function (duration) {
            if (duration) {
                var d = duration.days(),
                    h = duration.hours(),
                    m = duration.minutes(),
                    s = duration.seconds(),
                    y = duration.years(),
                    month = duration.months()
                var str = ''
                if (y) {
                    str += '<span class=\'countdown_section\'><span class=\'countdown_amount\'>' + y +
                        "</span><br>" + $t('Years') + " </span><span class='countdown_section'><span class='countdown_amount'>" + month +
                        "</span><br>" + $t('Months') + "</span>"
                } else if (month) {
                    str += '<span class=\'countdown_section\'><span class=\'countdown_amount\'>' + month + "</span><br>" + $t('Months') + "</span>"
                }
                $(this.countDownField)
                    .html('<span class="countdown_row countdown_show4">' + str + '<span class="countdown_section"><span class="countdown_amount">' + d +
                        "</span><br>" + $t('Days') + "</span><span class='countdown_section'><span class='countdown_amount'>" + h +
                        "</span><br>" + $t('Hours') + "</span><span class='countdown_section'><span class='countdown_amount'>"
                        + m + "</span><br>" + $t('Minutes') + "</span><span class='countdown_section'><span class='countdown_amount'>" + s +
                        "</span><br>" + $t('Seconds') + "</span>" + '</span>')
            }
        }
    };
});
