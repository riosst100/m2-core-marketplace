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
    'mage/translate'
], function ($) {
    'use strict';

    $.widget('mage.deleteItem', {

        options: {
            eventElement: '[data-role="del-button"]',
            updateElement: '[data-role="update-quote"]',
            saveAsDraftBtn: '[data-role="save-as-draft"]',
            wrapperAttachFiles: '[data-role="send-files"]',
            event: 'click',
            delBtnType: 'delete-file',
            attachmentId: null
        },

        /**
         * Build widget
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * @private
         */
        _bind: function () {
            this.element
                .find(this.options.eventElement)
                .on(this.options.event,  $.proxy(this._removeRow, this));

            if (this.element.data('action') === this.options.delBtnType) {
                this.element.on('click', $.proxy(this._handleRemoveFile, this));
            }
        },

        /**
         * @private
         */
        _removeRow: function () {
            this.element.remove();
            $(this.options.updateElement).trigger('updateTotal');
            $(this.options.updateElement).trigger('processEmptyGrid');
        },

        /**
         * @private
         */
        _handleRemoveFile: function () {
            this.element.closest('.attachments-item').remove();
            $(this.options.wrapperAttachFiles).trigger('clear');
            $(this.options.saveAsDraftBtn).trigger('setDelFiles', this.options.attachmentId);
        }
    });

    return $.mage.deleteItem;
});
