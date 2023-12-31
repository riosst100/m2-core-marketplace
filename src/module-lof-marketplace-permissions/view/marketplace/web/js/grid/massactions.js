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

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'mageUtils',
    'Magento_Ui/js/grid/massactions',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'mage/cookies'
], function ($, _, registry, utils, Massactions, confirm, customerData) {
    'use strict';

    return Massactions.extend({
        defaults: {
            templateTmp: '',
            imports: {
                tabName: '${ $.provider }:data.tabName'
            },
            listens: {
                tabName: 'onTabNameUpdated'
            },
            tracks: {
                template: true
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.templateTmp = this.template;
            this.template = '';
            window.FORM_KEY = $.mage.cookies.get('form_key');
        },

        /**
         * Hides massaction toolbar if not allowed
         *
         * @returns void
         */
        onTabNameUpdated: function () {
            if (this.isSellerAdmin()) {
                this.template = this.templateTmp;
            }

            if (!this.isSellerAdmin() && this.tabName === 'require_my_approval') {
                this.template = this.templateTmp;
            }
        },

        /**
         * Retrieves number of selected orders.
         *
         * @returns {Number}
         */
        selectedItemsNumber: function () {
            if (this.selections().totalSelected() === undefined) {
                return 0;
            }

            return this.selections().totalSelected();
        },

        /**
         * Retrieves status of approval button.
         *
         * @returns {Boolean}
         */
        isEnabled: function () {
            return this.selectedItemsNumber() !== 0;
        },

        /** @inheritdoc */
        _confirm: function (action, callback) {
            var confirmData = action.confirm,
                confirmMessage = confirmData.message;

            confirm({
                title: confirmData.title,
                content: confirmMessage,
                actions: {
                    confirm: callback
                }
            });
        },

        /**
         * Check if customer is seller admin.
         *
         * @returns {Boolean}
         */
        isSellerAdmin: function () {
            var sellerData = customerData.get('seller');

            return sellerData()['is_seller_admin'];
        }
    });
});
