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
    'Magento_Ui/js/form/element/ui-select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            listens: {
                'value': 'changeFormSubmitUrl'
            },
            modules: {
                formProvider: '${ $.provider }'
            }
        },

        /**
         * Change set parameter in save and validate urls of form
         *
         * @param {String|Number} value
         */
        changeFormSubmitUrl: function (value) {
            var pattern = /(set\/)(\d)*?\//,
                change = '$1' + value + '/';

            this.formProvider().client.urls.save = this.formProvider().client.urls.save.replace(pattern, change);
            this.formProvider().client.urls.beforeSave = this.formProvider().client.urls.beforeSave.replace(
                pattern,
                change
            );
        }
    });
});
