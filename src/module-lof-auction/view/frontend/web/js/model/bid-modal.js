/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    'use strict';

    return {
        bidLinkElement: '.bid_link',
        historyBidElement: '#lof_history_bid',

        /**
         * @param {String} url
         * @param {Object} params
         * @return {*}
         */
        initModal: function (bidLinkElement, historyBidElement) {
            var self = this;
            if (bidLinkElement) {
                this.bidLinkElement = bidLinkElement;
            }
            if (historyBidElement) {
                this.historyBidElement = historyBidElement;
            }
            if ($(this.historyBidElement).length <= 0) {
                return;
            }
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: $t('Bid History'),
                buttons: [
                    {
                        text: $.mage.__('Ok'),
                        class: '',
                        click: function () {
                            this.closeModal()
                        }
                    }]
            }
            modal(options, $(this.historyBidElement))
            $(this.bidLinkElement).on('click', function () {
                $(self.historyBidElement).modal('openModal')
            })
        }
    };
});
