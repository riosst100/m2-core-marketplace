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

tinyMCE.addI18n({en:{
    magentovariable:
    {
        insert_variable : "Insert Variable"
    }
}});

(function() {
    tinymce.create('tinymce.plugins.MagentovariablePlugin', {
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('mceMagentovariable', function() {
                var pluginSettings = ed.settings.magentoPluginsOptions.get('magentovariable');
                MagentovariablePlugin.setEditor(ed);
                MagentovariablePlugin.loadChooser(pluginSettings.url, null);
            });

            // Register Widget plugin button
            ed.addButton('magentovariable', {
                title : 'magentovariable.insert_variable',
                cmd : 'mceMagentovariable',
                image : url + '/img/icon.gif'
            });
        },

        getInfo : function() {
            return {
                longname : 'Magento Variable Manager Plugin for TinyMCE 3.x',
                author : 'Magento Core Team',
                authorurl : 'http://magentocommerce.com',
                infourl : 'http://magentocommerce.com',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('magentovariable', tinymce.plugins.MagentovariablePlugin);
})();
