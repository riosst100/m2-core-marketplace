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
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (_, registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            listens: {
                isConfigurable: 'handlePriceValue'
            },
            imports: {
                isConfigurable: '!ns = ${ $.ns }, index = configurable-matrix:isEmpty'
            },
            modules: {
                createConfigurableButton: '${$.createConfigurableButton}'
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();

            return this;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['content']);

            return this;
        },

        /**
         * Disable and clear price if product type changed to configurable
         *
         * @param {String} isConfigurable
         */
        handlePriceValue: function (isConfigurable) {
            if (isConfigurable) {
                this.disable();
                this.clear();
            }
        }
    });
});
