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
    'Lof_MarketPermissions/js/customer-data',
    'Magento_Ui/js/grid/listing'
], function (customerData, gridListing) {
    'use strict';

    return gridListing.extend({
        defaults: {
            template: 'Lof_MarketPermissions/grid/listing',
            selectableStatuses: []
        },

        /**
         * @return {*}
         */
        getTableClass: function () {
            return this['table_css_class'];
        },

        /**
         * Check if row is disabled for edit.
         *
         * @param {Object} row
         * @return {Boolean}
         */
        isRowEditDisabled: function (row) {


            if (this.isSellerAdmin()) {
                return !this.selectableStatuses.hasOwnProperty(row.status);
            }

            if (this.source.data.tabName === 'require_my_approval' &&
                (!this.selectableStatuses.hasOwnProperty(row.status) ||
                    row.approvedByMe !== undefined && row.approvedByMe
                )
            ) {
                return true;
            }
        },

        /**
         * Check if bulk actions allowed.
         *
         * @returns {Boolean}
         */
        isSellerAdmin: function () {
            var sellerData = customerData.get('seller');

            return sellerData()['is_seller_admin'];
        }
    });
});
