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

define([
    'jquery',
    'Magento_Ui/js/grid/columns/actions',
    'Lof_MarketPermissions/js/user-edit',
    'Lof_MarketPermissions/js/user-delete',
    'Lof_MarketPermissions/js/role-delete'
], function ($, Actions) {
    'use strict';

    return Actions.extend({
        defaults: {
            bodyTmpl: 'Lof_MarketPermissions/users/grid/columns/actions'
        },

        /**
         * Callback after click on element.
         *
         * @public
         */
        applyAction: function () {
            switch (this.type) {
                case 'edit-user':
                    $(this).userEdit(this.options)
                        .trigger('editUser');
                    break;

                case 'delete-user':
                    $(this).userDelete(this.options)
                        .trigger('deleteUser');
                    break;

                case 'delete-role':
                    $(this).roleDelete(this.options)
                        .trigger('deleteRole');
                    break;

                default:
                    return true;
            }
        }
    });
});
