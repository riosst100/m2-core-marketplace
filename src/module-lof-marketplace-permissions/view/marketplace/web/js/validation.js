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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

define(['jquery'], function ($) {
    'use strict';

    return function (target) {

        /**
         * @param {*} value
         * @param {HTMLElement} element
         * @return {*|jQuery}
         */
        var asyncValidate = function (value, element) {
            return $(element).data('async-is-valid');
        };

        $.validator.addMethod(
            'validate-async-seller-email',
            asyncValidate,
            $.mage.__('Seller with this email address already exists in the system. Enter a different email address to continue.') //eslint-disable-line max-len
        );

        $.validator.addMethod(
            'validate-async-seller-role-name',
            asyncValidate,
            $.mage.__('User role with this name already exists. Enter a different name to save this role.')
        );

        $.validator.addMethod(
            'validate-async-customer-email',
            asyncValidate,
            $.mage.__('User with this email already exists in the system. Enter a different email address to continue. If you want to use your current account as an administrator account, please contact the seller.') //eslint-disable-line max-len
        );

        return target;
    };
});
