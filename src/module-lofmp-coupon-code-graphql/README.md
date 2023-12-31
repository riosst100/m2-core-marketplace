# Magento 2 Coupon Code Marketplace Addon

    ``landofcoder/module-seller-coupon-graph-ql``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)

## Main Functionalities
- Seller Coupon
- The module allow Seller create sales rule, generate coupon code for customer via email
- Customer will purchase seller's products and apply his coupon code to get discount price.

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/Lofmp`
- Enable the module by running `php bin/magento module:enable Lofmp_CouponCodeGraphQl`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
- Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
- Install the module composer by running `composer require landofcoder/module-seller-coupon-graph-ql`
- enable the module by running `php bin/magento module:enable Lofmp_CouponCodeGraphQl`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Queries

1. My Coupon Codes

```
{
    myCouponCode (
        type: "all
        filters: {}
        pageSize: 10
        currentPage: 1
    ) {
        total_count
        items {
            coupon_id
            name
            alias
            code
            from_date
            to_date
            uses_per_customer
            discount_amount
            type
            times_used
            created_at
            expiration_date
            coupon_rule {
                rule_id
                rule_name
                discount_amount
            }
        }
    }
}
```

2. Get seller public coupon codes

```
{
    sellerCoupons (
        sellerUrl: String!
        filters: {}
        pageSize: 5
        currentPage: 1
    ) {
        total_count
        items {
            coupon_id
            name
            alias
            code
            from_date
            to_date
            uses_per_customer
            discount_amount
            type
            times_used
            created_at
            expiration_date
            coupon_rule {
                rule_id
                rule_name
                discount_amount
            }
        }
    }
}
```
