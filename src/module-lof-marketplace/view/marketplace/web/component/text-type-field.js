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
    'Magento_Ui/js/form/element/abstract'
], function ($, Abstract) {
    'use strict';

    return Abstract.extend({

        /**
         * Checks for relevant value
         *
         * @param {*} value
         * @returns {Boolean}
         */
        isRelevant: function (value) {
            if ($.inArray(value, ['field', 'area']) !== -1) {
                this.disabled(false);
                this.visible(true);

                return true;
            }

            this.reset();
            this.disabled(true);
            this.visible(false);

            return false;
        }
    });
});
