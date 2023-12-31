<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\DeliverySlot\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Lofmp\DeliverySlot\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    
        $setup->startSetup();
        /**
         * Add Delivery Time Slot Columns on Sales Order Grid
         * delivery_time_slot
         * delivery_date
         * delivery comment
         */
        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'delivery_time_slot',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => null,
                'comment' => 'Delivery Time Slot'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'delivery_date',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                'nullable' => true,
                'default' => null,
                'comment' => 'Delivery Date'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('sales_order_grid'),
            'delivery_comment',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'default' => null,
                'comment' => 'Delivery Comment'
            ]
        );

        $setup->endSetup();
    }
}
