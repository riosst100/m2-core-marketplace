define([
    'jquery',
    'jquery-ui-modules/widget',
    'Magento_Catalog/js/price-utils',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'mage/url',
    'Magento_Customer/js/model/customer',
    'mage/storage',
    'underscore',
    'validation'
    ], function(
        $, 
        jqueyUi,
        utils, 
        alert, 
        $t,
        mageurl,
        customer,
        storage
        ) {
        var deferred = $.Deferred();
        
        $.widget('lof.auctionMaxAbsenteeBid', {
            options: {
                wrapper: '#max_absentee_bid_wrapper',
                getUrl: '',
                auctionId: '0',
                priceFormat: ''
            },
            _create: function() {
               this._assignVariables();
               this._bindEvents();
            },
            _assignVariables: function() {
                var self = this, conf = this.options;
                this.$auctionAbsenteeWrapper = $(conf.wrapper);
                this.$auctionAbsenteeForm = $(conf.wrapper).find("form");
                this.$absenteeInput = $(conf.wrapper).find(".max-absentee-bid-input");
                this.$submitButton = $(conf.wrapper).find(".btn-place-max-bid");
                this.$maxBidAmountLabel = $(conf.wrapper).find(".max-bid-amount");
                this.$maxBidAmountPrice = $(conf.wrapper).find(".max-bid-price > .price");
                this.$inputBidField = $(conf.wrapper).parent().find(".mpbidding_amount");
                this.auctionId = conf.auctionId;
                this.getUrl = conf.getUrl;
                this.priceFormat = conf.priceFormat;
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
                            self.$maxBidAmountPrice.text(utils.formatPrice($(this).val(), self.priceFormat))
                        }
                    })
                }
                this._getCurrenMaxAbstenteBid();
            },
            _getCurrenMaxAbstenteBid: function() {
                var self = this
                if (this.getUrl) {
                    $.ajax({
                        url: this.getUrl,
                        dataType: 'json',
                        data: {"auction_id": this.auctionId},
                        type:'POST',
                        success: function(data) {
                            if (self.$absenteeInput && self.$absenteeInput.length > 0 && data && typeof(data.max_absentee_amount) != "undefined") {
                                var current_min_bid_amount = (typeof (self.$inputBidField) !== "undefined" && self.$inputBidField && self.$inputBidField.length > 0 ) ? self.$inputBidField.val() : data.max_absentee_amount;
                                
                                current_min_bid_amount = parseFloat(current_min_bid_amount) > parseFloat(data.max_absentee_amount) ? current_min_bid_amount :  data.max_absentee_amount;

                                self.$absenteeInput.val(self._formatDigitNumber(current_min_bid_amount));
                                self.$absenteeInput.change();
                            }
                        }
                    });
                }
            },
            _updateMaxAbsenteeBid: function() {
                var self = this, conf = this.options;
                var data = {};
                if (!self.$auctionAbsenteeForm.valid()) {
                    return false;
                }
                
                data.auctionId =  this.auctionId;
                data.maxBidAmount = this.$absenteeInput.val();
                
                self.$auctionAbsenteeForm.submit();
            }
        });
        return $.lof.auctionMaxAbsenteeBid;
    });