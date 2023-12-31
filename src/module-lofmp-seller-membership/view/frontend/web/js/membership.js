/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    "Magento_Ui/js/modal/alert",
    'priceBox',
    'jquery/ui',
    'jquery/jquery.parsequery',
], function ($, _, mageTemplate,utils,alert) {
    'use strict';
    
    $.widget('lofmp.membership', {
        options: {
            valueFieldSelector: '#lofmp_membership_duration',
            priceHolderSelector: '.price-box',
            durationOptions: {},
            priceFormat: null,
            priceTemplate: '<span class="price"><%- data.formatted %></span>'
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            // Initial setting of various option values
        	this._initializeOptions();
        	
        	/*Bind events*/
        	this._bindEvents();
        },
        
        /**
         * Initialize options
         */
        _initializeOptions: function(){        	
        	var durationOptions = this.options.durationOptions;
        	var valueField = $(this.options.valueFieldSelector);
        	$(durationOptions).each(function(index,option){
        		valueField.append($("<option/>", {
        	        value: option.value,
        	        text: option.label
        	    }));
        	});
        },
        
        /**
         * Bind events
         */
        _bindEvents: function(){
        	var options = this.options;
        	var valueField = $(this.options.valueFieldSelector);
    		$(valueField).on('change', this, this._changeDurationDropdown);
        },
        
        /**
         * Change credit dropdown.
         */
        _changeDurationDropdown: function(){
        	var options = $(this).membership('option');
        	if(options.priceFormat){
        		var priceFormat 	= options.priceFormat;
        		var priceTemplate 	= mageTemplate(options.priceTemplate);
        	}else{
        		var priceBoxOption 	= $(options.priceHolderSelector).priceBox('option');
        		var priceFormat 	= priceBoxOption.priceConfig.priceFormat;
	        	var priceTemplate 	= mageTemplate(priceBoxOption.priceTemplate);
        	}

        	var value = this.value;
        	var price = 0;
        	
        	$(options.durationOptions).each(function(index,option){
        		if(option.value == value){price = option.price;}
        	});
            var priceData = {formatted:utils.formatPrice(price, priceFormat)};
            $(options.priceHolderSelector+' [data-price-type="finalPrice"]').html(priceTemplate({data: priceData}));
        }
        
    });

    return $.lofmp.membership;
});
