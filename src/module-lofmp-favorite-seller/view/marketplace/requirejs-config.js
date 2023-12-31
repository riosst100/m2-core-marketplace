var config = {
    paths:{
        "jquery.dataTables":"https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min",
        "jquery.fancybox":"Lofmp_FavoriteSeller/js/jquery.fancybox.min",
        "jquery.tagsinput":"Lofmp_FavoriteSeller/js/jquery.tagsinput.min",
        "alertify":"Lofmp_FavoriteSeller/js/alertify.min",
        "ckeditor":"Lofmp_FavoriteSeller/js/ckeditor/ckeditor"
    },
    shim:{
        'jquery.dataTables':{
            'deps':['jquery']
        },
        'jquery.fancybox':{
            'deps':['jquery']
        },
        'jquery.tagsinput':{
            'deps':['jquery']
        },
        'alertify':{
            'deps':['jquery']
        },
        'ckeditor':{
            deps:['jquery'],
            exports: 'CKEDITOR'
        }
    }
};