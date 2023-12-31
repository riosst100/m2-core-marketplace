# Magento 2 Module Seller DeliverySlot

The module allow seller define delivery slot and vacation message, customer checkout order of seller can choose delivery time and comment to seller.

## How to setup?

The module require setup [Marketplace Core module](https://landofcoder.com/magento-2-marketplace-extension.html/) and [SplitCart Addon](https://landofcoder.com/magento-2-marketplace-split-cart.html/).

\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lofmp`
 - Enable the module by running `php bin/magento module:enable Lofmp_DeliverySlot`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-seller-delivery-slot`
 - enable the module by running `php bin/magento module:enable Lofmp_DeliverySlot`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
