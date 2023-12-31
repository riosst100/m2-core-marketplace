<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Plugin\Customer\Model\ResourceModel\Online\Grid;

use Magento\Customer\Model\ResourceModel\Online\Grid\Collection;

/**
 * Plugin for customer now online grid collection to display seller column.
 */
class CollectionPlugin
{
    /**
     * Before Load plugin.
     *
     * @param Collection $subject
     * @param bool $printQuery [optional]
     * @param bool $logQuery [optional]
     * @return array
     */
    public function beforeLoad(
        Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if (!$subject->isLoaded()) {
            $subject->getSelect()
                ->joinLeft(
                    ['seller_customer' => $subject->getTable('lof_marketplace_advanced_customer_entity')],
                    'main_table.customer_id = seller_customer.customer_id',
                    ['seller_id']
                );
            $subject->getSelect()
                ->joinLeft(
                    ['seller' => $subject->getTable('lof_marketplace_seller')],
                    'seller.seller_id = seller_customer.seller_id',
                    ['name']
                );
        }
        return [$printQuery, $logQuery];
    }
}
