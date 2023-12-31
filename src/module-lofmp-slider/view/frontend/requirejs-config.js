/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 var config = {
     paths: {
        'jquery_Customized': 'Lofmp_Slider/js/jquery.mobile.customized.min',
        'jquery_Easing': 'Lofmp_Slider/js/jquery.easing.1.3',
        'Camera': 'Lofmp_Slider/js/camera.min'
        // 'jquery.Bridget': 'Lofmp_Slider/js/jquery-bridget'
    },
 	shim: {
 		'Lofmp_Slider/js/jquery.mobile.customized.min': {
            'deps': ['jquery']
        },
    }
 };