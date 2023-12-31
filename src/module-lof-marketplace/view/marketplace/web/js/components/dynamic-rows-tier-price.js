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
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (_, DynamicRows) {
    'use strict';

    return DynamicRows.extend({

        /**
         * Init header elements
         */
        initHeader: function () {
            var labels;

            this._super();
            labels = _.clone(this.labels());
            labels = _.sortBy(labels, function (label) {
                return label.sortOrder;
            });

            this.labels(labels);
        }
    });
});
