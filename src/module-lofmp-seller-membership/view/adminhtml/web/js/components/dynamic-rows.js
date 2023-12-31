/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (DynamicRows) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
        	relatedCreditType : 2
        },
        initialize: function () {
            this._super();
        },
        handleCreditTypeChange: function(creditType){
        	this.visible(creditType == this.relatedCreditType);
        }
    });
});
