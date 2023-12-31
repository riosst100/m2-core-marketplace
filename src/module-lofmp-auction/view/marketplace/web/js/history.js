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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

require([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirmation) {
    var $select = $('select[name="status"]');
    $select.each(function () {
        var oldVar = $(this).val();
        $(this).on('change', function () {
            var $select = $(this);
            if ($select.val() == 1) {
                var status = 'Not Spam';
            } else {
                status = 'Spam';
            }
            confirmation({
                title: $.mage.__('Are you sure?'),
                content: $.mage.__('Do you want to set this bid to ' + status),
                actions: {
                    confirm: function () {
                        var $a = $select.closest("tr").find('.col-set_spam a');
                        var url = $a.attr('href');
                        $.ajax({
                            showLoader: true,
                            url: url,
                            data: {status: $select.val()},
                            type: "POST",
                            dataType: 'json'
                        });
                        oldVar = $select.val();
                    },
                    cancel: function () {
                        $select.val(oldVar);
                    }
                }
            });
        });
    })
})
