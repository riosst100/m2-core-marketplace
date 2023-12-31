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
    'ko',
    'Magento_Ui/js/grid/filters/filters'
], function (ko, gridFilters) {
    'use strict';

    return gridFilters.extend({
        defaults: {
            template: 'Lof_MarketPermissions/users/grid/filters/filters',
            showAllUsers: ko.observable(false),
            showActiveUsers: ko.observable(true)
        },

        /**
         * Sets filter for status field to 'active'.
         */
        setStatusActive: function () {
            this.showAllUsers(false);
            this.showActiveUsers(true);
            this.filters.status = this.params.statusActive;
            this.apply();
        },

        /**
         * Sets filter for status field to 'inactive'.
         */
        setStatusInactive: function () {
            this.showAllUsers(false);
            this.showActiveUsers(false);
            this.filters.status = this.params.statusInactive;
            this.apply();
        },

        /**
         * Clears filters data.
         */
        clear: function () {
            this.showAllUsers(true);
            this._super(null);
        }
    });
});
