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
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'mageUtils'
], function (DynamicRows, utils) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            dataProvider: '',
            insertData: [],
            listens: {
                'insertData': 'processingInsertData'
            }
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                    'insertData'
                ]);

            return this;
        },

        /**
         * Parsed data
         *
         * @param {Array} data - array with data
         * about selected records
         */
        processingInsertData: function (data) {
            if (!data.length) {
                return false;
            }

            data.each(function (options) {
                options.options.each(function (option) {
                    var path = this.dataScope + '.' + this.index + '.' + this.recordIterator,
                        curOption = utils.copy(option);

                    if (curOption.hasOwnProperty('sort_order')) {
                        delete curOption['sort_order'];
                    }

                    this.source.set(path, curOption);
                    this.addChild(curOption, false);
                }, this);
            }, this);
        },

        /**
         * Set empty array to dataProvider
         */
        clearDataProvider: function () {
            this.source.set(this.dataProvider, []);
        }
    });
});
