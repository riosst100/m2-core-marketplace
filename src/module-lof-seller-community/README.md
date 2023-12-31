# Landofcoder Seller Community Extension by [Landofcoder](https://landofcoder.com/magento2-extensions)

This module is compatible for Landofcoder Marketplace Addon extensions for Magento 2

## Requirements
  * Magento Community Edition 2.1.x-2.4.x or Magento Enterprise Edition 2.1.x-2.4.x

## Installation Method 1 - Installing via composer
  * Open command line
  * Using command "cd" navigate to your magento2 root directory
  * Run the commands:
  
```
composer require landofcoder/module-seller-community
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Installation Method 2 - Installing via FTP using archive
  * Download [ZIP Archive](https://github.com/landofcoder/module-seller-community/archive/master.zip)
  * Extract files
  * In your Magento 2 root directory create folder app/code/Lof/SellerCommunity
  * Copy files and folders from archive to that folder
  * In command line, using "cd", navigate to your Magento 2 root directory
  * Run the commands:
```
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```
