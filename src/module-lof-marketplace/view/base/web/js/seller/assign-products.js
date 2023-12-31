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

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            assignedProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            trRowIndex;

        $('in_adminassign_products').value = Object.toJSON(assignedProducts);

        /**
         * Register Category Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerAssignedProduct(grid, element, checked)
        {
            if (element.className != "admin__control-checkbox") {
                var trElement = jQuery('#' + element.id).parents('tr');
                trRowIndex = trElement.index();
                if (checked) {
                    assignedProducts.set(element.value, trRowIndex);
                } else {
                    assignedProducts.unset(element.value);
                }
                $('in_adminassign_products').value = Object.toJSON(assignedProducts);
                grid.reloadParams = {
                    'selected_products[]': assignedProducts.keys()
                };
            }
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function assignedProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;
            trRowIndex = trElement.rowIndex-2;
            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        gridJsObject.rowClickCallback = assignedProductRowClick;
        gridJsObject.checkboxCheckCallback = registerAssignedProduct;
    };
});
