/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
	map: {
		"*": {
			markerwithlabel: 'Lofmp_StoreLocator/js/libs/markerwithlabel',
			storelocator: 'Lofmp_StoreLocator/js/plugins/storelocator/jquery.storelocator',
			handlebars: 'Lofmp_StoreLocator/js/libs/handlebars.min',
			markerclusterer: 'Lofmp_StoreLocator/js/libs/markerclusterer.min',
			bootstrap: 'Lofmp_StoreLocator/js/libs/bootstrap.min'
		}
	},
	shim: {
    	'storelocator': {
            deps: ['handlebars', 'jquery']
        },
        'Lofmp_StoreLocator/js/plugins/storelocator/jquery.storelocator': {
        	deps: ['handlebars', 'jquery']	
        },
        'Lofmp_StoreLocator/js/libs/bootstrap.min': {
            deps: ['jquery']
        }
    }
};