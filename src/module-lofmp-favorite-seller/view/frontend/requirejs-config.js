var config = {
    paths:{
        "jquery.dataTables":"https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min",
        "alertify": "Lofmp_FavoriteSeller/js/alertify.min"
    },
    shim:{
        'jquery.dataTables':{
            'deps':['jquery']
        },
        'alertify':{
            'deps':['jquery']
        }
    }
};