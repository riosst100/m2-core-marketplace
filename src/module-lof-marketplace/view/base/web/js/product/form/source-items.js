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
    'Magento_Ui/js/form/components/fieldset',
    'uiRegistry',
    'underscore'
], function (Fieldset, registry, _) {
    'use strict';

    return Fieldset.extend({
        defaults: {
            advancedInventoryButtonIndex: '',
            imports: {
                onStockChange: '${ $.provider }:data.product.stock_data.manage_stock'
            }
        },

        /**
         * "Advanced Inventory" button should stay active in any case.
         *
         * @param {Integer} canManageStock
         */
        onStockChange: function (canManageStock) {
            var advancedInventoryButton = registry.get('index = ' + this.advancedInventoryButtonIndex);

            if (canManageStock === 0) {
                if (!_.isUndefined(advancedInventoryButton)) {
                    advancedInventoryButton.disabled(false);
                }
            }
        }
    });
});
