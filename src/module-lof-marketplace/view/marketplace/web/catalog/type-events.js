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
    'jquery',
    'Magento_Catalog/js/product/weight-handler'
], function ($, weight) {
    'use strict';

    return {
        $type: $('#product_type_id'),

        /**
         * Init
         */
        init: function () {

            if (weight.productHasWeight()) {
                this.type = {
                    virtual: 'virtual',
                    real: this.$type.val() //simple, configurable
                };
            } else {
                this.type = {
                    virtual: this.$type.val(), //downloadable, virtual, grouped, bundle
                    real: 'simple'
                };
            }
            this.type.current = this.$type.val();

            this.bindAll();
        },

        /**
         * Bind all
         */
        bindAll: function () {
            $(document).on('setTypeProduct', function (event, type) {
                this.setType(type);
            }.bind(this));

            //direct change type input
            this.$type.on('change', function () {
                this.type.current = this.$type.val();
                this._notifyType();
            }.bind(this));
        },

        /**
         * Set type
         * @param {String} type - type product (downloadable, simple, virtual ...)
         * @returns {*}
         */
        setType: function (type) {
            return this.$type.val(type || this.type.real).trigger('change');
        },

        /**
         * Notify type
         * @private
         */
        _notifyType: function () {
            $(document).trigger('changeTypeProduct', this.type);
        }
    };
});
