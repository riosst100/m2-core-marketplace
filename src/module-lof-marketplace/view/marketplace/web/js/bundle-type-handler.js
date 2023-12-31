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
    'Magento_Catalog/catalog/type-events',
    'Magento_Catalog/js/product/weight-handler'
], function ($, productType, weight) {
    'use strict';

    return {

        /**
         * Constructor component
         */
        'Magento_Bundle/js/bundle-type-handler': function () {
            this.bindAll();
            this._initType();
        },

        /**
         * Bind all
         */
        bindAll: function () {
            $(document).on('changeTypeProduct', this._initType.bind(this));
        },

        /**
         * Init type
         * @private
         */
        _initType: function () {
            if (
                productType.type.init === 'bundle' &&
                productType.type.current !== 'bundle' &&
                !weight.isLocked()
            ) {
                weight.switchWeight();
            }
        }
    };
});
