define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/lib/spinner',
    'rjsResolver',
    './adapter',
    'uiCollection',
    'mageUtils',
    'jquery',
    'Magento_Ui/js/core/app',
    'mage/validation'
], function (uiRegistry, _, loader, resolver, adapter, Collection, utils, $, app) {
    'use strict';

    var formFields = {
        init: function (config) {
            this.handleChangeCategories();
        },
        handleChangeCategories: function () {
            $(document).on('change', 'button[data-action="add-form-field"]', function () {
                var category_ids=uiRegistry.get('product_form.product_form.product-details.container_category_ids.category_ids').value();
                console.log(category_ids);

                return false;
            });
        }
    };

    return function (config) {
        $(document).ready(function () {
            formFields.init(config);
        });
    };
});