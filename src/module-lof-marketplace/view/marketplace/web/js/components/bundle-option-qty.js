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
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            valueUpdate: 'input',
            isInteger: true,
            validation: {
                'validate-number': true
            }
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            this.validation['validate-digits'] = this.isInteger;
            this._super();
        },

        /**
         * @inheritdoc
         */
        hasChanged: function () {
            var notEqual = this.value() !== this.initialValue.toString();

            return !this.visible() ? false : notEqual;
        }

    });
});
