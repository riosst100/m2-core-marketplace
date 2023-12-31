/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Lof_MarketPermissions/js/form/element/ui-group',
    'mage/translate',
    'mage/validation'
], function ($, UiGroup, $t) {
    'use strict';

    return UiGroup.extend({

        defaults: {
            selectedValues: [],
            filterInputPlaceholder: $t('Search by keyword'),
            isValidate: false
        },

        /**
         * Initializes UISelect component.
         *
         * @returns {UISelect} Chainable.
         */
        initialize: function () {
            this._super();
            this.selected(this.selectedValues);
            this.initialValue = this.selectedValues;
            this.isValidate = !$.isEmptyObject(this.validation);
            // validation ui-group
            if (this.isValidate) {
                this.initValidationRule();
            }

            return this;
        },

        /**
         * Calls 'initObservable' of parent, initializes 'options' and 'initialOptions'
         *     properties, calls 'setOptions' passing options to it
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super().observe([
                'filterInputPlaceholder'
            ]);

            return this;
        },

        /**
         * Add validation rule
         */
        initValidationRule: function () {
            $.validator.addMethod(
                'validate-select-field',
                function (value, element) {
                    var validateWrapper = $(element).parents('.action-select-wrap').find('.action-select');

                    if (value.length) {
                        $(validateWrapper).removeClass('_mage-error');

                        return true;
                    }

                    $(validateWrapper).addClass('_mage-error');

                    return false;
                },
                $.mage.__('This is a required field.')
            );
        },

        /**
         * Return input field name based on configuration
         *
         * @returns {String}
         */
        getInputFieldName: function () {
            return this.multiple ? this.inputName + '[]' : this.inputName;
        },

        /**
         * Return comma separated selected value
         *
         * @returns {String}
         */
        getValue: function () {
            if (this.getSelected().length) {
                return this.getSelected().map(function (item) {
                    return item.value;
                }).join();
            }
        }
    });
});
