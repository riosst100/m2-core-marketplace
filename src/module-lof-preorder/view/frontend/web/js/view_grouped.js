/*
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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

define([
    'jquery',
    "jquery/ui",
    'mage/translate',
    'Lof_PreOrder/js/view'
], function($, jUI, Translate) {
    'use strict';
    $.widget('preorder.viewGrouped', $.preorder.view, {
        options: {
            map: {}
        },
        _create: function(){
            var self = this;
            this._saveOriginal();
            $(document).ready(function () {
                var addtocartButton = self.options.addtocartButton;
                var addtocartButtonSpan = self.options.addtocartButtonSpan;
                var addToCartButtonLabel = $(self.options.addtocartButtonSpan).text();
                var count = 0;
                var isPreorder = self.options.flag;
                if (isPreorder == 1) {
                    self._setPreOrderLabel();
                }
                $(addtocartButton).click(function () {
                    count = 0;
                });
                $(addtocartButtonSpan).bind("DOMSubtreeModified",function () {
                    var title = $(this).text();
                    if (isPreorder == 1) {
                        if (title == addToCartButtonLabel) {
                            count++;
                            if (count == 1) {
                                self._setPreOrderLabel();
                            }
                        }
                    }
                });
                $.each(self.options.map, function(key, value){
                    if (value.flag == 1) {
                        var _msg = value.msg.replace(/\n/g, "<br />");
                        $('.qty input[name=super_group\\['+key+'\\]]').change(function(){
                                if(this.value > 0) {
                                    self.options.buttonText = value.buttonText;
                                    self.options.msg = value.msg;
                                    self.options.flag = value.flag;
                                    self._setPreOrderLabel();
                                } else {
                                    self._setDefaultLabel();
                                }
                        });
                        $('.grouped .price-box.price-final_price[data-product-id='+key+']').after(_msg);
                    }
                });
            });
        }
    });
    return $.preorder.viewGrouped;
});
