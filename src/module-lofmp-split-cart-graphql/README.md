# Magento 2 Split Cart Marketplace Addon

    ``landofcoder/module-split-cart-graph-ql``

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
- Enable the module by running `php bin/magento module:enable Lofmp_SplitCartGraphQl`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
- Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
- Install the module composer by running `composer require landofcoder/module-split-cart-graph-ql`
- enable the module by running `php bin/magento module:enable Lofmp_SplitCartGraphQl`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Support Queries

1. Init split cart before checkout

Example:

```
mutation {
    initCheckoutSplitCart(
      input: {
        seller_url: String!
        cart_id: String!
      }
    ) {
        id
        parent_id
        quote_id
        is_active
        is_ordered
    }
}
```
2. Checkout split cart

```
mutation {
    placeOrderSplitCart(
        sellerUrl: String
        input: { cart_id: String }
    ) {
        order {
            order_number
            order_id
        }
    }
}
```
3. Remove split cart when cancel checkout or customer logout

```
mutation {
    removeSplitCart(
        input: { cart_id: String }
    )
}
```
4. Get Seller Info for cart item

Example:
```
{
  customerCart {
    id
    total_quantity
    is_virtual
    items {
      uid
      id
      quantity
      product {
        id
        sku
        uid
        name
      }
      seller {
        seller_id
        seller_url
        seller_name
      }
    }
  }
}
```
5. Get Store config enable/disable split cart feature

```
{
    storeConfig {
        lofmp_splitcart_enabled
    }
}
```
