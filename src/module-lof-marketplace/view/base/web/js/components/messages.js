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
    'Magento_Ui/js/form/components/html'
], function (Html) {
    'use strict';

    return Html.extend({
        defaults: {
            form: '${ $.namespace }.${ $.namespace }',
            visible: false,
            imports: {
                responseData: '${ $.form }:responseData',
                visible: 'responseData.error',
                content: 'responseData.messages'
            },
            listens: {
                '${ $.provider }:data.reset': 'hide'
            }
        },

        /**
         * Show messages.
         */
        show: function () {
            this.visible(true);
        },

        /**
         * Hide messages.
         */
        hide: function () {
            this.visible(false);
        }
    });
});
