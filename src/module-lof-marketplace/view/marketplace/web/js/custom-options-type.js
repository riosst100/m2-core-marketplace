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
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/ui-select'
], function ($, _, registry, UiSelect) {
    'use strict';

    return UiSelect.extend({
        defaults: {
            previousGroup: null,
            groupsConfig: {},
            valuesMap: {},
            indexesMap: {},
            filterPlaceholder: 'ns = ${ $.ns }, parentScope = ${ $.parentScope }'
        },

        /**
         * Initialize component.
         * @returns {Element}
         */
        initialize: function () {
            return this
                ._super()
                .initMapping()
                .updateComponents(this.initialValue, true);
        },

        /**
         * Create additional mappings.
         *
         * @returns {Element}
         */
        initMapping: function () {
            _.each(this.groupsConfig, function (groupData, group) {
                _.each(groupData.values, function (value) {
                    this.valuesMap[value] = group;
                }, this);

                _.each(groupData.indexes, function (index) {
                    if (!this.indexesMap[index]) {
                        this.indexesMap[index] = [];
                    }

                    this.indexesMap[index].push(group);
                }, this);
            }, this);

            return this;
        },

        /**
         * Callback that fires when 'value' property is updated.
         *
         * @param {String} currentValue
         * @returns {*}
         */
        onUpdate: function (currentValue) {
            this.updateComponents(currentValue);

            return this._super();
        },

        /**
         * Show, hide or clear components based on the current type value.
         *
         * @param {String} currentValue
         * @param {Boolean} isInitialization
         * @returns {Element}
         */
        updateComponents: function (currentValue, isInitialization) {
            var currentGroup = this.valuesMap[currentValue];

            if (currentGroup !== this.previousGroup) {
                _.each(this.indexesMap, function (groups, index) {
                    var template = this.filterPlaceholder + ', index = ' + index,
                        visible = groups.indexOf(currentGroup) !== -1,
                        component;

                    switch (index) {
                        case 'container_type_static':
                        case 'values':
                            template = 'ns=' + this.ns +
                                ', dataScope=' + this.parentScope +
                                ', index=' + index;
                            break;
                    }

                    /*eslint-disable max-depth */
                    if (isInitialization) {
                        registry.async(template)(
                            function (currentComponent) {
                                currentComponent.visible(visible);
                            }
                        );
                    } else {
                        component = registry.get(template);

                        if (component) {
                            component.visible(visible);

                            /*eslint-disable max-depth */
                            if (_.isFunction(component.clear)) {
                                component.clear();
                            }
                        }
                    }
                }, this);

                this.previousGroup = currentGroup;
            }

            return this;
        }
    });
});
