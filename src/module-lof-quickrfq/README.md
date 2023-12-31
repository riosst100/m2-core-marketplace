# MAGENTO 2 QUICK REQUEST FOR QUOTE

    ``landofcoder/module-quickrfq``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)

## Main Functionalities
Link: https://landofcoder.com/magento-2-request-for-quote.html

Magento 2 Request for Quote allows customers to quickly submit an RFQ for single products. The store admin can manage and reply any customer quotation from the back-end. When new quotes are created, this Magento 2 quote extension will send email notifications to both merchants and customers.

This extension is an essential B2C & B2B solution for Magento 2.

- One-click Magento 2 request a quote
- Submit Quick RFQ form for a single product
- Check list of requested quotes in grid
- Notify customers by messages after quote submission
- Send email to Customer when a quote is created
- Check message history and reply merchants fast
- Notify Admin when customers submit a quote
- Reply to customers after getting customer quotation
- Support REST API for Quick RFQ FEATURED
- Manage all RFQs from back-end
- Configure RFQ options, email options, upload restrictions & google captcha keys

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Lof`
 - Enable the module by running `php bin/magento module:enable Lof_Quickrfq`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require landofcoder/module-quickrfq`
 - enable the module by running `php bin/magento module:enable Lof_Quickrfq`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`
