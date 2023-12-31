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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $setup->getConnection();
        //Update for version 1.0.1
        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            if (!$setup->getConnection()->isTableExists($installer->getTable('lofmp_marketplace_cancel_request'))) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('lofmp_marketplace_cancel_request')
                )->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'identity' => true,
                        'primary' => true
                    ],
                    'Entity ID'
                )->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Membership Comment'
                )->addColumn(
                    'duration',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Membership Comment'
                )->addColumn(
                    'price',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    [],
                    'Price'
                )->addColumn(
                    'membership_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    [
                    'nullable' => false
                    ],
                    'Membership Id'
                )->addColumn(
                    'product_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'identity' => false,
                        'unsigned' => true,
                        'nullable' => false,
                    ],
                    'Product Id'
                )->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Request status: 0 - pending, 1 - approved, 2 - checking, 3 - declided'
                )->addColumn(
                    'customer_comment',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Customer Comment'
                )->addColumn(
                    'admin_comment',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Admin Comment'
                )->addColumn(
                    'creation_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Request Creation Time'
                );
                $installer->getConnection()->createTable($table);
            }

            $lofmp_marketplace_membership = $setup->getTable('lofmp_marketplace_membership');
            $lofmp_marketplace_membership_transaction = $setup->getTable('lofmp_marketplace_membership_transaction');
            $column = [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Invoice Product Id',
                'default' => '0'
            ];
            $column2 = [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Invoice Product Options',
                'length'  => '64k',
                'default' => ''
            ];
            $column3 = [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Invoice Item Id',
                'default' => '0'
            ];
            $column4 = [
                'type' => Table::TYPE_INTEGER,
                'nullable' => true,
                'comment' => 'Membership Group Id',
                'default' => '0'
            ];

            if ($setup->getConnection()->isTableExists($lofmp_marketplace_membership) == true) {
                $connection->addColumn($lofmp_marketplace_membership, 'product_id', $column);
                $connection->addColumn($lofmp_marketplace_membership, 'product_options', $column2);
                $connection->addColumn($lofmp_marketplace_membership, 'item_id', $column3);
            }
            if ($setup->getConnection()->isTableExists($lofmp_marketplace_membership_transaction) == true) {
                $connection->addColumn($lofmp_marketplace_membership_transaction, 'product_id', $column);
                $connection->addColumn($lofmp_marketplace_membership_transaction, 'product_options', $column2);
                $connection->addColumn($lofmp_marketplace_membership_transaction, 'item_id', $column3);
                $connection->addColumn($lofmp_marketplace_membership_transaction, 'group_id', $column4);

                $setup->getConnection()->addColumn(
                    $setup->getTable('lofmp_marketplace_membership_transaction'),
                    'order_increment_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => true,
                        'default' => '',
                        'comment' => 'order_increment_id',
                    ]
                );

                $setup->getConnection()->addColumn(
                    $setup->getTable('lofmp_marketplace_membership_transaction'),
                    'order_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length' => '10',
                        'identity' => false,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => 'order_id',
                    ]
                );
            }

            if ($setup->getConnection()->isTableExists($lofmp_marketplace_membership) == true && $setup->getConnection()->isTableExists($lofmp_marketplace_membership_transaction) == true) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('lofmp_marketplace_membership'),
                    'before_seller_group_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => true,
                        'comment' => "Before seller group id"
                    ]
                );

                $setup->getConnection()->modifyColumn(
                    $setup->getTable('lofmp_marketplace_membership'),
                    'seller_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => "Seller ID"
                    ]
                );

                $setup->getConnection()->modifyColumn(
                    $setup->getTable('lofmp_marketplace_membership_transaction'),
                    'seller_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => false,
                        'comment' => "Seller ID"
                    ]
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_marketplace_membership', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
                    $setup->getTable('lofmp_marketplace_membership'),
                    'seller_id',
                    $setup->getTable('lof_marketplace_seller'),
                    'seller_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_marketplace_membership_transaction', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
                    $setup->getTable('lofmp_marketplace_membership_transaction'),
                    'seller_id',
                    $setup->getTable('lof_marketplace_seller'),
                    'seller_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_marketplace_membership_transaction', 'order_id', 'sales_order', 'entity_id'),
                    $setup->getTable('lofmp_marketplace_membership_transaction'),
                    'order_id',
                    $setup->getTable('sales_order'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        }
        $installer->endSetup();
    }//end upgrade()
}//end class
