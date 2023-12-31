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

(function ($) {
  "use strict";
	if (Prototype.BrowserFeatures.ElementExtensions) {
	  	var disablePrototypeJS = function (method, pluginsToDisable) {
	  		var handler = function (event) {
	  			event.target[method] = undefined;
	  			setTimeout(function () {
	  				delete event.target[method];
	  			}, 0);
	  		};
	  		pluginsToDisable.each(function (plugin) {
	  			$(window).on(method + '.bs.' + plugin, handler);
	  		});
		},
	  	pluginsToDisable = ['collapse', 'dropdown'/*, 'modal'*/, 'tooltip', 'popover', 'tab'];
		disablePrototypeJS('show', pluginsToDisable);
		disablePrototypeJS('hide', pluginsToDisable);
	}
}(jQuery));
