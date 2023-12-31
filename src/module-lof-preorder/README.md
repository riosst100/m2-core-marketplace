# Magento 2 Pre-Order Extension

    ``landofcoder/module-pre-order``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)

## Main Functionalities
Link: https://landofcoder.com/magento-2-preorder-extension.html

Magento 2 Pre Order extension allows customers to place orders for products that are not yet available for immediate purchase and delivery. With the support of this module, you can achieve massive sales from out-of-stock and coming soon products.

A great extension to analyze and meet the demands of your customers

- [New] Display preorder note on the shopping cart, checkout page, category page
- [New] Show warning message when not enough item qty
- [New] View preorder message & warning note in My Orders
- [New] Customize preorder message for each product FEATURED
- [New] Support pre-order for bundle products FEATURED
- Preorder upcoming products & out of stock products
- Replace ‘Add to cart’ button with ‘Pre-order’ one both on product & category pages
- Pre-order message on product page FEATURED
- Customer can pay full or partial payment
- Automatic 'Product is back', 'Back in Stock' notifications
- Set preorder status and availability date
- Automatic Email Notification
- Pre-order settings & display options
- [New] Support REST API

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_PreOrder`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-pre-order`
 - enable the module by running `php bin/magento module:enable Lof_PreOrder`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
