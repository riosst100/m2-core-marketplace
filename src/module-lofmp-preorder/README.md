# Magento 2 Module Seller PreOrder

The module allow seller manage pre order

## How to setup?

The module require setup [Marketplace Core module](https://landofcoder.com/magento-2-marketplace-extension.html/) and [SplitCart Addon](https://landofcoder.com/magento-2-marketplace-split-cart.html/).

\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lofmp`
 - Enable the module by running `php bin/magento module:enable Lofmp_PreOrder`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-seller-pre-order`
 - enable the module by running `php bin/magento module:enable Lofmp_PreOrder`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
