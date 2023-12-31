# Magento 2 Split Cart Marketplace Addon

    ``landofcoder/module-split-cart``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)

## Main Functionalities
Link: https://landofcoder.com/magento-2-marketplace-split-cart.html

With magento 2 split order marketplace addon, it’s easy for customers to add many products from different sellers into cart when shopping in your marketplace.
Anytime they go shopping on your marketplace, they can quickly add any desired products into cart and take process to checkout.

- Cart will be divided based on sellers (if buyer add three products from 3 sellers then cart will be split it among 3 cart based on seller )
- Rest seller product will be removed during checkout from the cart and only single seller’s checkout will be validated
- Admin can easily manage orders for vendors
- Customer can choose different payment methods for different vendors
- Customer can set different shipping address for different vendor's order
- Admin can enable /disable the Marketplace Split Cart option from the back-end

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/Lof`
- Enable the module by running `php bin/magento module:enable Lofmp_SplitCart`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
- Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
- Install the module composer by running `composer require landofcoder/module-split-cart`
- enable the module by running `php bin/magento module:enable Lofmp_SplitCart`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### REST API

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
```

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

## TODO
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
