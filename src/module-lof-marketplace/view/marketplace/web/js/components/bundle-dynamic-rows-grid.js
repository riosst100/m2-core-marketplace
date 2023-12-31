/**
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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

define([
    'underscore',
    'Magento_Ui/js/dynamic-rows/dynamic-rows-grid'
], function (_, dynamicRowsGrid) {
    'use strict';

    return dynamicRowsGrid.extend({
        defaults: {
            label: '',
            columnsHeader: false,
            columnsHeaderAfterRender: true,
            addButton: false
        },

        /**
         * Initialize elements from grid
         *
         * @param {Array} data
         *
         * @returns {Object} Chainable.
         */
        initElements: function (data) {
            var newData = this.getNewData(data),
                recordIndex;

            this.parsePagesData(data);
            this.templates.record.bundleOptionsDataScope = this.dataScope;

            if (newData.length) {
                if (this.insertData().length) {
                    recordIndex = data.length - newData.length - 1;

                    _.each(newData, function (newRecord) {
                        this.processingAddChild(newRecord, ++recordIndex, newRecord[this.identificationProperty]);
                    }, this);
                }
            }

            return this;
        },

        /**
         * Mapping value from grid
         *
         * @param {Array} data
         */
        mappingValue: function (data) {
            if (_.isEmpty(data)) {
                return;
            }

            this._super();
        }
    });
});
