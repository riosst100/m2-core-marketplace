# Magento 2 Table Rate Shipping Marketplace Addon

    ``landofcoder/module-seller-table-rate-shipping``

- [Main Functionalities](#markdown-header-main-functionalities)
- [Installation](#markdown-header-installation)

## Main Functionalities
Link: https://landofcoder.com/magento-2-marketplace-table-rate-shipping.html

Magento 2 Table Rate Shipping marketplace addon is an extension of magento 2 marketplace, a necessary module for the sellers.
Not only is Marketplace Table Rate Shipping For Magento 2 module allows the seller to add the shipping rates through the CSV files from their end but it also is helpful for confirming order, tracking number from seller and print invoice.
Moreover, Marketplace Table Rate Shipping module is assistance to the seller in managing the shipping table rate in the easiest way.

- Sellers/admin can control shipping rates by importing CSV file
- Assign shipping rates by entering the seller id
- Create sub-methods for Table Rate Shipping.
- Create Super Shipping Set Easily
- Calculate Shipping based on ZIP and weight of the product
- Shipping cost will be calculated as per the seller
- Shipping rates will populate based on CSV during checkout.
- Option to export shipping CSV file from the back-end.
- Add Invoice & Packing slip address, VAT, TAX information.
- Download Invoice & Shipping slip in the blink of an eye.
- Easily Customization
- Supports alphanumeric zip codes
- Support Multi Store, Multi Language
- User-Friendly interface

Notice: Since, the add-on is dependent on the Lof Multi-Vendor Marketplace extension and Vendor Multi Shipping add-on,
the admin has to install the Lof Multi-Vendor Marketplace extension and the Vendor Multi Shipping add-on before installing the add-on.

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

- Unzip the zip file in `app/code/Lofmp`
- Enable the module by running `php bin/magento module:enable Lofmp_TableRateShipping`
- Apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

- Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
- Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
- Install the module composer by running `composer require landofcoder/module-seller-table-rate-shipping`
- enable the module by running `php bin/magento module:enable Lofmp_TableRateShipping`
- apply database updates by running `php bin/magento setup:upgrade`\*
- Flush the cache by running `php bin/magento cache:flush`
