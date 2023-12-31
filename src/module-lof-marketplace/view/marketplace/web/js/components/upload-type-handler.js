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
    'Magento_Ui/js/form/element/select',
    'uiRegistry'
], function (Select, registry) {
    'use strict';

    return Select.extend({
        defaults: {
            listens: {
                value: 'changeTypeUpload'
            },
            typeUrl: 'file',
            typeFile: 'link_url',
            filterPlaceholder: 'ns = ${ $.ns }, parentScope = ${ $.parentScope }'
        },

        /**
         * Initialize component.
         * @returns {Element}
         */
        initialize: function () {
            return this
                ._super()
                .changeTypeUpload(this.initialValue);
        },

        /**
         * Callback that fires when 'value' property is updated.
         *
         * @param {String} currentValue
         * @returns {*}
         */
        onUpdate: function (currentValue) {
            this.changeTypeUpload(currentValue);

            return this._super();
        },

        /**
         * Change visibility for typeUrl/typeFile based on current value.
         *
         * @param {String} currentValue
         */
        changeTypeUpload: function (currentValue) {
            var componentFile = this.filterPlaceholder + ', index=' + this.typeFile,
                componentUrl = this.filterPlaceholder + ', index=' + this.typeUrl;

            switch (currentValue) {

                case 'file':
                    this.changeVisible(componentFile, true);
                    this.changeVisible(componentUrl, false);
                    break;

                case 'url':
                    this.changeVisible(componentFile, false);
                    this.changeVisible(componentUrl, true);
                    break;
            }
        },

        /**
         * Change visible
         *
         * @param {String} filter
         * @param {Boolean} visible
         */
        changeVisible: function (filter, visible) {
            registry.async(filter)(
                function (currentComponent) {
                    currentComponent.visible(visible);
                }
            );
        }
    });
});
