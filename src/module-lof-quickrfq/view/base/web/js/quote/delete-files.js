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

define(['jquery'], function ($) {
    'use strict';

    $.widget('mage.deleteFiles', {
        options: {
            delButton: '[data-role="delete-button"]',
            delElement: '',
            parentElement: '',
            wrapper: '[data-role="send-files"]'
        },

        /**
         * Create widget.
         * @type {Object}
         */
        _create: function () {
            if (!this.options.delElement) {
                this.options.delElement = this.element;
            }

            if (typeof this.options.delElement !== 'object') {
                this.options.delElement = $(this.options.delElement);
            }
            this._bind();
        },

        /**
         * Add events on items.
         * @private
         */
        _bind: function () {
            var deleteButton = this.options.delElement.find(this.options.delButton);

            deleteButton.on('click', $.proxy(this._delFiles, this));
        },

        /**
         * Delete element.
         * @private
         */
        _delFiles: function (e) {
            e.stopPropagation();
            this.options.delElement.remove();
            this.options.parentElement.remove();
            $(this.options.wrapper).trigger('clear');
        }

    });

    return $.mage.deleteFiles;
});
