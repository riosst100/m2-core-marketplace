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

var config = {
    map: {
        '*': {
            "fastclick":            'Lof_MarketPlace/js/fastclick',
            "editTrigger":          'Lof_MarketPlace/js/edit-trigger',
            "addClass":             'Lof_MarketPlace/js/add-class',
            "translateInline":      "mage/translate-inline",
            "form":                 "mage/backend/form",
            "button":               "mage/backend/button",
            "accordion":            "mage/accordion",
            "actionLink":           "mage/backend/action-link",
            "validation":           "mage/backend/validation",
            "notification":         "mage/backend/notification",
            "loader":               "mage/loader_old",
            "loaderAjax":           "mage/loader_old",
            "floatingHeader":       "mage/backend/floating-header",
            "suggest":              "mage/backend/suggest",
            "mediabrowser":         "jquery/jstree/jquery.jstree",
            "tabs":                 "mage/backend/tabs",
            "treeSuggest":          "mage/backend/tree-suggest",
            "calendar":             "mage/calendar",
            "dropdown":             "mage/dropdown_old",
            "collapsible":          "mage/collapsible",
            "jstree":               "jquery/jstree/jquery.jstree",
            "details":              "jquery/jquery.details",
            "validate":             "jquery/jquery.validate",
            'icheck':               'Lof_MarketPlace/js/icheck',
            'dataTables-bootstrap': 'Lof_MarketPlace/js/dataTables.bootstrap',
            'raphael':          "Lof_MarketPlace/js/raphael",
            'morris':          "Lof_MarketPlace/js/morris",
            'jquery-ui-modules/widget':           'jquery/ui',
            'jquery-ui-modules/core':             'jquery/ui',
            'jquery-ui-modules/accordion':        'jquery/ui',
            'jquery-ui-modules/autocomplete':     'jquery/ui',
            'jquery-ui-modules/button':           'jquery/ui',
            'jquery-ui-modules/datepicker':       'jquery/ui',
            'jquery-ui-modules/dialog':           'jquery/ui',
            'jquery-ui-modules/draggable':        'jquery/ui',
            'jquery-ui-modules/droppable':        'jquery/ui',
            'jquery-ui-modules/effect-blind':     'jquery/ui',
            'jquery-ui-modules/effect-bounce':    'jquery/ui',
            'jquery-ui-modules/effect-clip':      'jquery/ui',
            'jquery-ui-modules/effect-drop':      'jquery/ui',
            'jquery-ui-modules/effect-explode':   'jquery/ui',
            'jquery-ui-modules/effect-fade':      'jquery/ui',
            'jquery-ui-modules/effect-fold':      'jquery/ui',
            'jquery-ui-modules/effect-highlight': 'jquery/ui',
            'jquery-ui-modules/effect-scale':     'jquery/ui',
            'jquery-ui-modules/effect-pulsate':   'jquery/ui',
            'jquery-ui-modules/effect-shake':     'jquery/ui',
            'jquery-ui-modules/effect-slide':     'jquery/ui',
            'jquery-ui-modules/effect-transfer':  'jquery/ui',
            'jquery-ui-modules/effect':           'jquery/ui',
            'jquery-ui-modules/menu':             'jquery/ui',
            'jquery-ui-modules/mouse':            'jquery/ui',
            'jquery-ui-modules/position':         'jquery/ui',
            'jquery-ui-modules/progressbar':      'jquery/ui',
            'jquery-ui-modules/resizable':        'jquery/ui',
            'jquery-ui-modules/selectable':       'jquery/ui',
            'jquery-ui-modules/slider':           'jquery/ui',
            'jquery-ui-modules/sortable':         'jquery/ui',
            'jquery-ui-modules/spinner':          'jquery/ui',
            'jquery-ui-modules/tabs':             'jquery/ui',
            'jquery-ui-modules/tooltip':          'jquery/ui',
            categoryForm:       'Lof_MarketPlace/catalog/category/form',
            newCategoryDialog:  'Lof_MarketPlace/js/new-category-dialog',
            categoryTree:       'Lof_MarketPlace/js/category-tree',
            productGallery:     'Lof_MarketPlace/js/product-gallery',
            baseImage:          'Lof_MarketPlace/catalog/base-image-uploader',
            newVideoDialog:     'Lof_MarketPlace/js/video/new-video-dialog',
            openVideoModal:     'Lof_MarketPlace/js/video/video-modal',
            productAttributes:  'Lof_MarketPlace/catalog/product-attributes',
            menu:               'mage/backend/menu',
            sellerOrderShipment: 'Lof_MarketPlace/js/order/shipment',
            mageTranslationDictionary: 'Magento_Translation/js/mage-translation-dictionary',
            regionUpdater:   'Magento_Checkout/js/region-updater'
        }
    },
    "shim": {
        "jquery/bootstrap": ["jquery","jquery/ui"],
        "custom": ["jquery","jquery/bootstrap"],
        "jquery/custom": ["jquery","jquery/bootstrap"],
        "jquery/slimscroll": ["jquery"],
        "jquery/dataTables": ["jquery"],
        "jquery/vmap": ["jquery"],
        "jquery/vmap.world": ["jquery"],
        "jquery/vmap.sampledata": ["jquery"],
        "jquery/fix_prototype_bootstrap": ["jquery","jquery/bootstrap","prototype"],
        "productGallery": ["jquery/fix_prototype_bootstrap"],
        "Lof_MarketPlace/catalog/apply-to-type-switcher": ["Lof_MarketPlace/catalog/type-events"],
        // 'jquery/blueimp_gallery': ["jquery","prototype"]
    },
    "deps": [
        "mage/backend/bootstrap",
        "mage/adminhtml/globals",
        "fastclick",
        // 'jquery/blueimp_gallery',
        "jquery/bootstrap",
        'icheck',
        "jquery/slimscroll",
        "jquery/dataTables",
         // "jquery/vmap",
         // "jquery/vmap.world",
         // "jquery/vmap.sampledata",
         'dataTables-bootstrap',
         'raphael',
         'morris',
         "jquery/custom",
         "jquery/fix_prototype_bootstrap",
         // 'load_image',
         /*'canvas_to_blob',*/
        'mage/translate-inline',
        'mageTranslationDictionary'

         ],
    "paths": {
        /*'prototype': 'prototype/prototype',*/
        /*"jquery/ui": "jquery/jquery-ui-1.9.2",*/
        "jquery/ui": "jquery/jquery-ui",
        // 'jquery/blueimp_gallery':    "Lof_MarketPlace/js/jquery.blueimp-gallery",
        "jquery/bootstrap": "Lof_MarketPlace/js/bootstrap",
        "jquery/slimscroll": 'Lof_MarketPlace/js/jquery.slimscroll',
        "jquery/dataTables": 'Lof_MarketPlace/js/jquery.dataTables',
        // "jquery/vmap": 'Lof_MarketPlace/js/jquery.vmap',
        // "jquery/vmap.world": 'Lof_MarketPlace/js/jquery.vmap.world',
        // "jquery/vmap.sampledata": 'Lof_MarketPlace/js/jquery.vmap.sampledata',
        "jquery/custom": "Lof_MarketPlace/js/custom",
        "jquery/fix_prototype_bootstrap": "Lof_MarketPlace/js/fix_prototype_bootstrap",
        "Magento_Catalog/catalog/type-events": "Lof_MarketPlace/catalog/type-events",
        "Magento_Catalog/catalog/apply-to-type-switcher": "Lof_MarketPlace/catalog/apply-to-type-switcher",
        "Magento_Catalog/js/product/weight-handler":"Lof_MarketPlace/js/product/weight-handler",
        "Magento_Catalog/js/product-gallery":"Lof_MarketPlace/js/product-gallery",
        "Magento_ProductVideo/js/get-video-information":"Lof_MarketPlace/js/video/get-video-information",
        "Magento_Catalog/js/components/website-currency-symbol":"Lof_MarketPlace/js/components/website-currency-symbol",
        "Magento_Catalog/js/tier-price/value-type-select":"Lof_MarketPlace/js/tier-price/value-type-select",
        "Magento_Catalog/js/tier-price/percentage-processor":"Lof_MarketPlace/js/tier-price/percentage-processor",
        "Magento_Catalog/js/utils/percentage-price-calculator":"Lof_MarketPlace/js/utils/percentage-price-calculator",
        "Magento_InventoryCatalogAdminUi/js/product/form/source-items":"Lof_MarketPlace/js/product/form/source-items",
        "Magento_InventoryConfigurableProductAdminUi/js/components/sources-visibility-checker":"Lof_MarketPlace/js/components/sources-visibility-checker",
        "Magento_InventorySalesAdminUi/product/form/fieldset":"Lof_MarketPlace/product/form/fieldset",
    }
};
