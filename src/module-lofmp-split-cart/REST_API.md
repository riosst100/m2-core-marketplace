### REST API Doc

Use rest api as steps

1. *STEP 1* - Before checkout cart of seller should call init quote before

1.1 For Guest

EndPoint: `/V1/splitCartGuest/:cartId/:sellerUrl/init-checkout`
Method: `POST`
Params:
- :cartId - string - cart id
- :sellerUrl - string - seller url key

Response: Split Quote Data Interface

1.2 For Logged In Customer

EndPoint: `/V1/splitCart/mine/:sellerUrl/init-checkout`
Method: `POST`
Params:
- :cartId - int - current cart id of logged in customer
- :sellerUrl - string - seller url key

Response: Split Quote Data Interface

2. *STEP 2* - Save Payment and Create Order (place order api for split cart)

2.1 For Guest

EndPoint: `/V1/splitCartGuest/:cartId/:sellerUrl/payment-information`
Method: `POST`
Params:
- :cartId - string - marked guest cart id
- :sellerUrl - string - seller url key
Body Data: Payment Information

```
{
    "paymentMethod": {
        "method": int - selected payment method id
    },
    "email": string - address email,
    "firstname": string - address firstname,
    "lastname": string - address lastname
}

Response: int - Order Id

2.2 For Logged In Customer

EndPoint: `/V1/splitCart/mine/payment-information/:sellerUrl`
Method: `POST`
Params:
- :cartId - int - current cart id of logged in customer
- :sellerUrl - string - seller url key

Body Data: Payment Information

```
{
    "paymentMethod": {
        "method": int - selected payment method id
    },
    "billing_address": address data interface
}
```

Response: int - Order Id

3. DELETE remove split cart for parent cart

Use REST API when customer logout or customer cancel checkout for split cart

3.1 For Guest

EndPoint: `/V1/splitCartGuest/remove/:cartId`
Method: `DELETE`
Params:
- :cartId - string - marked guest cart id

Response: bool

3.2 For Logged In Customer

EndPoint: `/V1/splitCart/mine/remove`
Method: `DELETE`
Params:
- :cartId - int - the shopping cart ID

Response: bool

- Add seller id, seller url to response cart item (DONE)

Current response cart data as this:

```
{
    "id": int,
    "items": [
        {
            "item_id": int,
            "sku": string,
            "qty": int,
            "name": string,
            "price": int,
            "product_type": string,
            "quote_id": string
        },
        ...
    ],
    "items_count": int,
    "items_qty": int,
    "customer": Customer information data,
    "billing_address": Address information data,
    "store_id": int,
    "customer_is_guest": bool
    ...
}
```

New cart data as this:

```
{
    "id": int,
    "items": [
        {
            "item_id": int,
            "sku": string,
            "qty": int,
            "name": string,
            "price": int,
            "product_type": string,
            "quote_id": string,
            "extension_attributes": {
                "seller_id": int,
                "seller_url": string,
                "seller_name": string
            }
        },
        ...
    ],
    "items_count": int,
    "items_qty": int,
    "customer": Customer information data,
    "billing_address": Address information data,
    "store_id": int,
    "customer_is_guest": bool
    ...
}
