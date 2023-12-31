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
