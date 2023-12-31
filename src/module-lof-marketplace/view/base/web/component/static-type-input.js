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
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function (registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            parentOption: null
        },

        /**
         * Initialize component.
         *
         * @returns {Element}
         */
        initialize: function () {
            return this
                ._super()
                .initLinkToParent();
        },

        /**
         * Cache link to parent component - option holder.
         *
         * @returns {Element}
         */
        initLinkToParent: function () {
            var pathToParent = this.parentName.replace(/(\.[^.]*){2}$/, '');

            this.parentOption = registry.async(pathToParent);
            this.value() && this.parentOption('label', this.value());

            return this;
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.parentOption(function (component) {
                component.set('label', value ? value : component.get('headerLabel'));
            });

            return this._super();
        }
    });
});
