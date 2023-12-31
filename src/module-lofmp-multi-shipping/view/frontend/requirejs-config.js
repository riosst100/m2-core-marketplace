/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
var sellermultishipping_mixin_enabled = true;

var config = {
     map: {
         '*': {
             regionUpdater:   'Magento_Checkout/js/region-updater',
             // "Magento_Checkout/js/view/shipping": "Lofmp_MultiShipping/js/view/shipping"
         }
     },
     config: {
         mixins: {
             'Magento_Checkout/js/view/shipping': {
                 'Lofmp_MultiShipping/js/mixin/shipping-view-mixin': sellermultishipping_mixin_enabled
             }
             // 'Magento_Checkout/js/view/summary/shipping': {
             //     'Lofmp_MultiShipping/js/mixin/summary-shipping-mixin': sellermultishipping_mixin_enabled
             // },
         }
     }
};
