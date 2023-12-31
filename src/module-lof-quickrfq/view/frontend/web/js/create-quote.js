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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'underscore',
    'mage/template',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, _) {
    'use strict';
    var options = {
        type: 'popup',
        responsive: true,
        innerScroll: true,
        title: 'Terms And Conditions',
        buttons: [{
            text: $.mage.__('Cancel'),
            class: '',
            click: function () {
                this.closeModal();
            }
        }]
    };
    var popup = modal(options, $('#popup-terms'));
    $("#click").on('click', function () {
        $("#popup-terms").modal("openModal");
    });
    $.widget('mage.lofquickrfqCreateQuote', {
        options: {
            url: '',
            isAjax: false,
            popupSelector: '#lof-quote-popup',
            formSelector: '[data-role="lof-quote-popup"]',
            popupTitles: {
                quoteRequest: $.mage.__('Request a Quote')
            },
            popup: '',
            closeButton: '.cancel-quote-request',
            saveButton: '[data-action="save-quote"]',
            urlPost: 'product_id/',
            createQuoteUrl: '',
            lofFormButtonSelector: '#lof-quote-request-button',
            attachedFiles: '[data-role="add-file"]',
            form: '[data-action="create-lof-quote-form"]'
        },

        /**
         *
         * @private
         */
        _create: function () {
            var options;

            options = {
                'type': 'popup',
                'modalClass': 'lof-popup-request-quote',
                'focus': '[data-role="lof-quote-popup"] .textarea',
                'responsive': true,
                'innerScroll': true,
                'title': this.options.popupTitles.quoteRequest,
                'buttons': []
            };

            this._bind();
            $(this.element).modal(options);
        },

        /**
         *
         * @private
         */
        _bind: function () {
            $(this.options.lofFormButtonSelector).on('click', $.proxy(function () {
                this.showModal();
            }, this));
            $(this.options.closeButton).on('click', $.proxy(function (e) {
                e.preventDefault();
                this.closeModal();
            }, this));

            this.clickSubmit();
        },

        /**
         *
         * @private
         */
        showModal: function () {
            $(this.element).modal('openModal');
        },

        /**
         *
         * @private
         */
        closeModal: function () {
            $(this.element).modal('closeModal');
        },
        /**
         *
         * @private
         */
        clickSubmit: function () {
            $('#add-quote-form button.save').on('click',function () {
                if (this.options.form.valid()) {
                    $(this).addClass('submitting');
                }
            })
        },
    });

    return $.mage.lofquickrfqCreateQuote;
});
