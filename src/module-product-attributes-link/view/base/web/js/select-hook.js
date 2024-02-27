define([
    'jquery',
    'uiRegistry'
], function ($, uiRegistry) {
    'use strict';

    return function (Select) {
        return Select.extend({
            /**
             * Callback that fires when 'value' property is updated.
             */
            onUpdate: function () {
                this._super();

                var option_id = this.value._latestValue;
                var code = this.code;
                var categoryIdsField = uiRegistry.get('product_form.product_form.product-details.container_category_ids.category_ids');
                var category_id = categoryIdsField.value._latestValue;
                var attributeSetField = uiRegistry.get('product_form.product_form.product-details.attribute_set_id');
                var attribute_set_id = attributeSetField.value._latestValue;
                var mappingGroupIdField = uiRegistry.get('product_form.product_form.product-details.mapping_group_id');
                var mapping_group_id = mappingGroupIdField.value;
                var mappingPriorityField = uiRegistry.get('product_form.product_form.product-details.mapping_priority');
                var mapping_priority = mappingPriorityField.value;
                this.getAttributesMapping(option_id, category_id, attribute_set_id, code, mapping_group_id, mapping_priority);
            },
            getAttributesMapping: function (option_id, category_id, attribute_set_id, code, mapping_group_id, mapping_priority) {
                $('body').trigger('processStart');
        
                $.ajax({
                    url: '/marketplace/productattributeslink/mappingdata/attributes',
                    data: {
                        form_key: window.FORM_KEY,
                        category_id: category_id,
                        option_id: option_id,
                        attribute_set_id: attribute_set_id,
                        code: code,
                        mapping_group_id: mapping_group_id,
                        mapping_priority: mapping_priority
                    },
                    dataType: 'json',
                    success: function (resp) {
                        if (resp.data) {
                            var attributeSetField = uiRegistry.get('product_form.product_form.product-details.attribute_set_id');
                            if (resp.data.attribute_set_id && resp.data.attribute_set_id != null) {
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