# Magento 2 MarketPlace Bundle Product

    ``landofcoder/marketproductbundle``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Pro Version](#markdown-header-pro-version)
 - [Installation](#markdown-header-installation)

## Main Functionalities
Link: https://landofcoder.com/magento-2-marketplace-extension.html

Magento 2 Multi vendor Marketplace Extension helps to convert your eCommerce store into a completely active multi-vendor marketplace like Amazon, Etsy or eBay, etc with multiple vendors and customers conducting transactions.

Take your Magento 2 store to the next level by letting unlimited suppliers/vendors upload, sell and manage their products.
At the same time, boost the shopping experience of your customers by giving them more choices to buy from numerous products of different categories. Also, customers are allowed to give reviews & rating for any seller, vendor's products & service.
Rest API is supported.

- Fast Marketplace Interface & Experience
- Flexible Magento 2 Themes Compatibility
- Multiple Payment Gateways Integrated
- Advanced Report Supported
- Flexible to Setup Commission for Sellers
- Display feedbacks - reviews from customers
- Well-organized and attractive marketplace page
- Professional Vendor Shop Page
- Approve sellers & products automatically/ manually
- Support Seller Credit Accounts
- Show Seller Info on Product Details
- Upload Multiple Products In Bulk
- Manage order, transactions smartly
- Powerful SEO Management
- Instant Vendor Messaging System
- [New] Support PWA, REST API, GraphQL

## Pro Version
More features? Follow Pro Version here:
Link: https://landofcoder.com/magento-2-marketplace-pro.html

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_MarketProductBundle`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/marketproductbundle`
 - enable the module by running `php bin/magento module:enable Lof_MarketProductBundle`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
