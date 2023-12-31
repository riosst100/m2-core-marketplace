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
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
    	defaults: {
            bodyTmpl: 'ui/grid/cells/html'
    	},
        /*eslint-disable eqeqeq*/
        /**
         * Retrieves label associated with a provided value.
         *
         * @returns {String}
         */
        getLabel: function (record) {
            //console.log(record);
        	var value = record[this.index];
        	var percent = value / 5 * 100;
        	return '<div id="summary-rating" class="field-summary-rating"><div class="rating-box"><div class="rating" style="width:'
        			+ percent + '%;"></div></div></div>';
        }

        /*eslint-enable eqeqeq*/
    });
});
