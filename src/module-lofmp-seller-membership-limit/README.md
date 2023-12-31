# Mage2 Module Lofmp Seller Membership Limit

## Require
The addons is require there extensions:
- Lof MarketPlace
- Lofmp SellerMembership
- Lof Auction
- Lofmp Auction

## General
    ``landofcoder/seller-membership-limit``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)


## Main Functionalities
Seller Upload Their Identification to be Approved

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lofmp`
 - Enable the module by running `php bin/magento module:enable Lofmp_SellerMembershipLimit`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/seller-membership-limit`
 - enable the module by running `php bin/magento module:enable Lofmp_SellerMembershipLimit`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

