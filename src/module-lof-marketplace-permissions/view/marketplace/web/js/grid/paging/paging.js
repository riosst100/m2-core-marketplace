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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

define([
    'Magento_Ui/js/grid/paging/paging'
], function (gridPaging) {
    'use strict';

    return gridPaging.extend({
        defaults: {
            template: 'Lof_MarketPermissions/grid/paging/paging',
            sizesConfig: {
                template: 'Lof_MarketPermissions/grid/paging/sizes'
            }
        },

        /**
         * @return {Number}
         */
        getFirstNum: function () {
            return this.pageSize * (this.current - 1) + 1;
        },

        /**
         * @return {*}
         */
        getLastNum: function () {
            if (this.isLast()) {
                return this.totalRecords;
            }

            return this.pageSize * this.current;
        },

        /**
         * @return {Array}
         */
        getPages: function () {
            var pagesList = [],
                i;

            for (i = 1; i <= this.pages; i++) {
                pagesList.push(i);
            }

            return pagesList;
        }
    });
});
