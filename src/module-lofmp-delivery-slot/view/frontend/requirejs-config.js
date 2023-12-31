var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Lofmp_DeliverySlot/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Lofmp_DeliverySlot/js/model/set-payment-information-mixin': true
            }
        }
    }
};