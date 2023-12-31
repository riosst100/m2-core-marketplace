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

/**
 * @api
 * @deprecated since version 2.2.0
 */
define([
    'Magento_Ui/js/form/components/button',
    'uiRegistry',
    'underscore'
], function (Button, registry, _) {
    'use strict';

    return Button.extend({
        defaults: {
            currentRecordNamespace: 'bundle_current_record',
            listingDataProvider: '',
            value: [],
            imports: {
                currentRecordName: '${ $.provider }:${ $.currentRecordNamespace }',
                listingData: '${ $.provider }:${ $.listingDataProvider }'
            },
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            listens: {
                listingData: 'setListingData'
            }
        },

        /**
         * Initializes component.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            this._super()
                .initSource();

            return this;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                    'value',
                    'listingData'
                ]);

            return this;
        },

        /**
         * Calls 'destroy' of parent and
         * clear listing provider source
         *
         * @returns {Object} Chainable.
         */
        destroy: function () {
            this._super();
            this.source.set(this.listingDataProvider, []);

            return this;
        },

        /**
         * Call parent "action" method
         * and set new data to record and listing.
         *
         * @returns {Object} Chainable.
         */

        action: function () {
            this._super();
            this.source.set(this.currentRecordNamespace, this.name);
            this.source.set(this.listingDataProvider, this.value());

            return this;
        },

        /**
         * Init current source.
         *
         * @returns {Object} Chainable.
         */
        initSource: function () {
            if (!_.isFunction(this.source)) {
                this.source = registry.get(this.provider);
            }

            return this;
        },

        /**
         * Set data to listing source.
         *
         * @returns {Object} Chainable.
         */
        setListingData: function (data) {
            if (this.name === this.currentRecordName) {
                this.source.set(this.dataScope, data);
            }

            return this;
        }
    });
});
