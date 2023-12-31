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
    'uiComponent'
], function (_, registry, component) {
    'use strict';

    return component.extend({
        defaults: {
            sourcesIndex: ''
        },

        /**
         * Hide source tab if convert product to configurable and show it if to simple.
         */
        applySourcesConfiguration: function (visibleMatrix) {
            var source = registry.get('index = ' + this.sourcesIndex);

            if (!_.isUndefined(source)) {
                source.visible(!visibleMatrix);
            }
        }
    });
});
