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
    'text!Lof_Quickrfq/template/quote/files.html',
    'Lof_Quickrfq/js/quote/delete-files',
    'mage/translate',
    'mage/msie/file-reader'
], function ($, mageTemplate, filesTpl) {
    'use strict';

    $.widget('mage.addFile', {
        options: {
            wrapFiles: '[data-role="added-files"]',
            parent: '[data-role="send-files"]',
            errorText: $.mage.__('Your file could not be uploaded. Please try again.'),
            labelText: $.mage.__('Delete')
        },

        /**
         * Create widget.
         * @type {Object}
         */
        _create: function () {
            this.filesTemplate = mageTemplate(filesTpl);

            if (!$(this.element).parents('.field-attachment').hasClass('_disabled')) {
                this._bind();
            } else {
                $(this.element).find('input').prop('disabled', true);
            }
        },

        /**
         * Add events on items.
         * @private
         */
        _bind: function () {
            this._on({
                'change': this._getFiles,
                'keypress': this._setChange
            });
        },

        /**
         * Open input after press key enter for label focus
         *
         * @param {jQuery.Event} e
         * @private
         */
        _setChange: function (e) {
            if (e.keyCode === 13) {
                $(e.target).find('input').trigger('click');
            }
        },

        /**
         * Get files from input
         * @private
         */
        _getFiles: function (e) {
            var files = $(e.target.files),
                reader = new FileReader();

            this._addFiles(files[0]);
            reader.onprogress = $.proxy(this._addProgress, this);
            reader.onerror = $.proxy(this._addError, this);
            reader.onload = $.proxy(this._loaded, this);
            reader.readAsBinaryString(files[0]);
        },

        /**
         * Wath for progress
         * @private
         */
        _addProgress: function (e) {
            var percentLoaded;

            if (e.lengthComputable) {
                percentLoaded = Math.round(e.loaded / e.total * 100);
                this._updateProgress(percentLoaded);
            }
        },

        /**
         * Add error in load.
         * @private
         */
        _addError: function () {
            $(this.options.parent).trigger('error', {
                text: this.options.errorText
            });
            this._removeProgress();
        },

        /**
         * Update progress param.
         * @private
         */
        _updateProgress: function (progress) {
            $(this.options.parent).attr('data-progress', progress + '%');
            $(this.options.parent).addClass('loading');
        },

        /**
         * Callback loaded file;
         * @private
         */
        _loaded: function () {
            this._removeProgress();
        },

        /**
         * Remove progress param;
         * @private
         */
        _removeProgress: function () {
            $(this.options.parent).attr('data-progress', '');
            $(this.options.parent).removeClass('loading');
        },

        /**
         * @param {Object} data
         * @private
         */
        _addFiles: function (data) {
            var fileData = {
                    label: this.options.labelText,
                    name: data.name
                },
                file = $(this.filesTemplate({
                    data: fileData
                }));

            file.deleteFiles({
                parentElement: this.element
            });
            $(this.options.wrapFiles).append(file.last());
            this.element.hide();
            $(this.options.parent).trigger('add', {
                data: data,
                element: this.element,
                fileName: file
            });
        }
    });

    return $.mage.addFile;
});
