# Magento 2 Flat Rate Shipping Marketplace Addon

    ``landofcoder/module-seller-flat-rate-shipping``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)

## Main Functionalities
Link: https://landofcoder.com/magento-2-marketplace-flat-shipping-rate.html

Magento 2 Marketplace Advanced Flat Rate Shipping addon is one-stop solution for a flexible and easy-to-customize module to offer flat shipping cost options to beat your competitors while improving customers' buying experience.
In particular, it is highly applicable to multi vendor store, which incredibly simplifies and eases your way into managing shipping price regardless of how big your store is.

- Flat rate could be applied by fixed amount or percent amount.
- Import and export the rate in CSV files
- Create Unlimited Custom Flat Rate Shipping Methods
- Display custom error message
- Enable or disable flat rate shipping methods with ease
- Allow On Specific Countries Or All Countries
- Flat Rate can be defined on per order basis or per item basis condition
- Easy to Configure

Notice: Since, the add-on is dependent on the Lof Multi-Vendor Marketplace extension and Vendor Multi Shipping add-on,
the admin has to install the Lof Multi-Vendor Marketplace extension and the Vendor Multi Shipping add-on before installing the add-on.

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/Lofmp`
- Enable the module by running `php bin/magento module:enable Lofmp_FlatRateShipping`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
- Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
- Install the module composer by running `composer require landofcoder/module-seller-flat-rate-shipping`
- enable the module by running `php bin/magento module:enable Lofmp_FlatRateShipping`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`
