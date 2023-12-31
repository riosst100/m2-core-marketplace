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
    'Magento_Ui/js/dynamic-rows/record',
    'uiRegistry'
], function (Record, registry) {
    'use strict';

    return Record.extend({
        /**
         * @param {String} val - type of Input Type
         */
        onTypeChanged: function (val) {
            var columnVisibility  = !(val === 'multi' || val === 'checkbox');

            registry.async(this.name + '.' + 'selection_can_change_qty')(function (elem) {
                elem.visible(columnVisibility);
            });
        }
    });
});
