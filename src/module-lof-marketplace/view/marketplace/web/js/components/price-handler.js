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
    'Magento_Ui/js/form/element/abstract'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            linksPurchasedSeparately: '0',
            useDefaultPrice: false,
            listens: {
                linksPurchasedSeparately: 'changeDisabledStatus',
                useDefaultPrice: 'changeDisabledStatus'
            }
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();
            this.changeDisabledStatus();

            return this;
        },

        /**
         * Disable/enable price field
         */
        changeDisabledStatus: function () {
            if (this.linksPurchasedSeparately === '1') {
                if (this.useDefaultPrice) {
                    this.disabled(true);
                } else {
                    this.disabled(false);
                }
            } else {
                this.disabled(true);
            }
        }
    });
});
