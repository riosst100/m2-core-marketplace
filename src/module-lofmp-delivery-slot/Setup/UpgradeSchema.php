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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @package Lofmp\DeliverySlot\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
    
        $setup->startSetup();
        /**
         * Add Delivery Time Slot Columns on Sales Order
         * delivery_time_slot
         * delivery_date
         * delivery comment
         */
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'delivery_time_slot',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => null,
                    'comment' => 'Delivery Time Slot'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'delivery_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery Date'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'delivery_comment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => null,
                    'comment' => 'Delivery Comment'
                ]
            );
        }

        /**
         * Remove Delivery Time Slot Columns on Sales Order Grid
         * delivery_time_slot
         * delivery_date
         * delivery comment
         */
        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $setup->getConnection()->dropColumn(
                $setup->getTable('sales_order_grid'),
                'delivery_time_slot'
            );
            $setup->getConnection()->dropColumn(
                $setup->getTable('sales_order_grid'),
                'delivery_date'
            );

            $setup->getConnection()->dropColumn(
                $setup->getTable('sales_order_grid'),
                'delivery_comment'
            );
        }

        /**
         * Delivery Slot Group Table
         */
        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('delivery_slot_group'))
                ->addColumn(
                    'group_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Group Id'
                )
                ->addColumn(
                    'slot_group_name',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'default' => ''
                    ],
                    'Slot Group Name'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'unsigned' => true,
                        'nullable' => true
                    ],
                    'Seller Id'
                )
                ->addColumn(
                    'zip_code',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'default' => '',
                        'unique' => true
                    ],
                    'Zip Code'
                )
                ->setComment('Delivery Slot Group Table');
            $setup->getConnection()->createTable($table);
        }
        
        /**
         * Delivery Slot Table
         */
        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $table = $setup->getConnection()
                ->newTable($setup->getTable('delivery_slot'))
                ->addColumn(
                    'slot_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Slot Id'
                )->addColumn(
                    'parent_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'unsigned' => true,
                    ],
                    'Parent Id'
                )->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    11,
                    [
                        'unsigned' => true,
                        'nullable' => true
                    ],
                    'Seller Id'
                )->addColumn(
                    'day',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Day'
                )->addColumn(
                    'start_time',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Start Time'
                )->addColumn(
                    'end_time',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'End Time'
                )->addColumn(
                    'end_time',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'End Time'
                )->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Status'
                )->addColumn(
                    'allocation',
                    Table::TYPE_INTEGER,
                    8,
                    [
                        'unsigned' => true,
                        'default' => 1
                    ],
                    'Allocation Limit number orders on each delivery slots. Available for delivery for a slot will check total orders in slot < slot allocation.'
                )->addForeignKey(
                    $setup->getFkName('delivery_slot', 'parent_id', 'delivery_slot_group', 'group_id'),
                    'parent_id',
                    $setup->getTable('delivery_slot_group'),
                    'group_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment('Delivery Slot');
            $setup->getConnection()->createTable($table);
        }

        /**
         * modify column type
         */
        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            // Get module table
            $setup->getConnection()->modifyColumn(
                $setup->getTable('delivery_slot_group'),
                'zip_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Zip Code'
                ]
            );
        }

        /**
         * Add Delivery Time Slot Columns on Sales Order
         * delivery_slot_id
         */
        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'delivery_slot_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                    'nullable' => false,
                    'comment' => 'Delivery Slot Id'
                ]
            );
        }

        /**
         * Add Column on delivery_slot_group
         *
         */
        if (version_compare($context->getVersion(), '0.0.8', '<')) {
        }

        /**
         * Add Column on delivery_slot_group
         *
         */
        /**
         * Add Delivery Time Slot Columns on Quote
         * delivery_time_slot
         * delivery_date
         * delivery comment
         */
        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'delivery_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery Date'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'delivery_comment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => null,
                    'comment' => 'Delivery Comment'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'delivery_slot_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                    'nullable' => false,
                    'comment' => 'Delivery Slot Id'
                ]
            );
        }
        if (version_compare($context->getVersion(), '0.0.10', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('delivery_slot'),
                'seller_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Seller Id'
                ]
            );
            
            $setup->getConnection()->addColumn(
                $setup->getTable('delivery_slot_group'),
                'seller_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Seller Id'
                ]
            );

            $setup->getConnection()->addForeignKey(
                $setup->getFkName('delivery_slot', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
                'delivery_slot',
                'seller_id',
                $setup->getTable('lof_marketplace_seller'),
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $setup->getConnection()->addForeignKey(
                $setup->getFkName('delivery_slot_group', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
                'delivery_slot_group',
                'seller_id',
                $setup->getTable('lof_marketplace_seller'),
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }
        /**
         * Version 0.0.11
         */
        if (version_compare($context->getVersion(), '0.0.11', '<')) {
            $setup->getConnection()->addColumn(
                    $setup->getTable('lof_marketplace_sellerorder'),
                    'delivery_time_slot',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'default' => null,
                        'comment' => 'Delivery Time Slot'
                    ]
                );
                
            $setup->getConnection()->addColumn(
                $setup->getTable('lof_marketplace_sellerorder'),
                'delivery_date',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery Date'
                ]
            );
    
            $setup->getConnection()->addColumn(
                $setup->getTable('lof_marketplace_sellerorder'),
                'delivery_comment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'default' => null,
                    'comment' => 'Delivery Comment'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('lof_marketplace_sellerorder'),
                'delivery_slot_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                    'nullable' => true,
                    'comment' => 'Delivery Slot Id'
                ]
            );
        }
        $setup->endSetup();
    }
}
