define([
    'jquery',
    'uiRegistry'
], function ($, uiRegistry) {
    'use strict';

    return function (Select) {
        return Select.extend({
            toggleOptionSelected: function (data) {
                this._super();

                if (this.code == "category_ids") {
                    var category_id = this.value._latestValue;
                    var mappingGroupIdField = uiRegistry.get('product_form.product_form.product-details.mapping_group_id');
                    var mapping_group_id = mappingGroupIdField.value;
                    var mappingPriorityField = uiRegistry.get('product_form.product_form.product-details.mapping_priority');
                    var mapping_priority = mappingPriorityField.value;
                    this.getCategoryAttributeSetLink(category_id, mapping_group_id, mapping_priority);
                }
            },
            getCategoryAttributeSetLink: function (category_id, mapping_group_id, mapping_priority) {
        
                $('body').trigger('processStart');

                var attributeSetField = uiRegistry.get('product_form.product_form.product-details.attribute_set_id');
                attributeSetField.toggleOptionSelected({value: ""});
        
                $.ajax({
                    url: '/marketplace/productattributeslink/mappingdata/categories',
                    data: {
                        form_key: window.FORM_KEY,
                        category_id: category_id,
                        mapping_group_id: mapping_group_id,
                        mapping_priority: mapping_priority
                    },
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.data) {
                            if (resp.data.attribute_set_id) {
                                var attributeSetField = uiRegistry.get('product_form.product_form.product-details.attribute_set_id');
                                attributeSetField.toggleOptionSelected({value: resp.data.attribute_set_id});
                            }

                            if (resp.data.mapping_group_id) {
                                var mappingGroupIdField = uiRegistry.get('product_form.product_form.product-details.mapping_group_id');
                                mappingGroupIdField.value = resp.data.mapping_group_id;
                            }

                            if (resp.data.mapping_priority) {
                                var mappingPriorityField = uiRegistry.get('product_form.product_form.product-details.mapping_priority');
                                mappingPriorityField.value = resp.data.mapping_priority;
                            }
                        }
                    },
                    complete: function () {
                        $('body').trigger('processStop');
                    }
                });
        
                return true;
            }
        });
    }
});