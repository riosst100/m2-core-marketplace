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
            valueUpdate: 'input'
        },

        /**
         * Change validator
         */
        handleChanges: function (value) {
            var isDigits = value !== 1;

            this.validation['validate-number'] = !isDigits;
            this.validation['validate-digits'] = isDigits;
            this.validation['less-than-equals-to'] = isDigits ? 99999999 : 99999999.9999;
            this.validate();
        }
    });
});
