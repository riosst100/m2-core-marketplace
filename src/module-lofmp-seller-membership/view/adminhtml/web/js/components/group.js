/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/components/group',
], function (Group) {
    'use strict';

    return Group.extend({
    	defaults: {
    		relatedCreditType : 1
    	},
        /**
         * Extends this with defaults and config.
         * Then calls initObservable, iniListenes and extractData methods.
         */
        initialize: function () {
            this._super();
            return this;
        },
        
        handleCreditTypeChange: function(creditType){
        	this.visible(creditType == this.relatedCreditType);
        }
    });
});
