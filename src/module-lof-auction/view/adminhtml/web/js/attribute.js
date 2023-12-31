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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

 /*jshint jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";
    $.widget('auction.setattr', {
        _create: function () {
            var aucType = this.options.auctionType;
            aucType = aucType.split(',');
            aucType.each(function (i) {
                $('select[name="product[auction_type]"]  option[value="'+i+'"]').attr('selected', 'selected');
            });
        }
    });
    return $.auction.setattr;
});
