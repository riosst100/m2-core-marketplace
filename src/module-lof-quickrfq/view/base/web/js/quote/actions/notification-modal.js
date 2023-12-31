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
    'mage/template',
    'text!Lof_Quickrfq/template/quote/notification-modal.html',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, mageTemplate, modalTpl) {
    'use strict';

    $.widget('mage.notificationModal', {
        options: {
            text: '',
            title: '',
            modalOptions: null
        },

        /**
         * Build widget
         *
         * @private
         */
        _create: function () {
            this._setModal();
            this._bind();
        },

        /**
         * Bind events
         *
         * @private
         */
        _bind: function () {
            this.element.on('notification', $.proxy(this._showModal, this));
        },

        /**
         * Set notification modal.
         *
         * @private
         */
        _setModal: function () {
            var popupOptions = {
                'type': 'popup',
                'modalClass': 'restriction-modal-quote',
                'responsive': true,
                'innerScroll': true,
                'title': $.mage.__(this.options.title),
                'buttons': [{
                    class: 'action-primary confirm action-accept',
                    type: 'button',
                    text: 'Ok',

                    /** Click action */
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            this.options.modalOptions = this.options.modalOptions || popupOptions;
            this.modalBlock = $(mageTemplate(modalTpl)({
                data: this.options.text
            }));
            this.modalBlock = this.modalBlock[this.modalBlock.length - 1];
            $(this.modalBlock).modal(this.options.modalOptions);
        },

        /**
         * Open notification modal.
         *
         * @private
         */
        _showModal: function () {
            $(this.modalBlock).modal('openModal');
        }
    });

    return $.mage.notificationModal;
});
