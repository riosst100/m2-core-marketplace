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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Class UpgradeSchema
 * @package Lof\Auction\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $installer = $setup;
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $subscriberTable = $installer->getConnection()
                ->newTable($installer->getTable('lof_auction_subscriber'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity Id'
                )->addColumn(
                    'auction_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Auction Id'
                )->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id'
                )->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Email'
                )->addColumn(
                    'is_sent',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Is Sent Email Notify'
                );

            $historyTable = $installer->getConnection()
                ->newTable($installer->getTable('lof_auction_history'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity Id'
                )->addColumn(
                    'amount_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Amount Id'
                )->addColumn(
                    'auction_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Auction Id'
                )->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id'
                )->addColumn(
                    'auction_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['unsigned' => true, 'nullable' => false],
                    'Auction Amount'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Created At'
                )->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Status'
                );

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_product'),
                'max_amount',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Max Amount'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_amount'),
                'is_spam',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Is Spam'
                ]
            );

            $installer->getConnection()->changeColumn(
                $installer->getTable('lof_auction_product'),
                'reserve_price',
                'reserve_price',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'comment' => 'reserve_price.'
                ]
            );

            $installer->getConnection()->createTable($subscriberTable);
            $installer->getConnection()->createTable($historyTable);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $mixAmount = $installer->getConnection()
                ->newTable($installer->getTable('lof_auction_mix'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity Id'
                )->addColumn(
                    'auction_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Auction Id'
                )->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id'
                )->addColumn(
                    'amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['unsigned' => true, 'nullable' => false],
                    'Auction Amount'
                )->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Created At'
                )->addColumn(
                    'is_spam',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Is Spam'
                )->addColumn(
                    'product_id',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product Id'
                );
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_amount'),
                'updated_at',
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
                ]
            );
            $installer->getConnection()->createTable($mixAmount);

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_amount'),
                'restrict_count',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Restrict Count'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auto_auction'),
                'restrict_count',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Restrict Count'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_history'),
                'is_auto',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Is Auto'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auto_auction'),
                'is_spam',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Is Spam'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auto_auction'),
                'updated_at',
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_mix'),
                'winning_status',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Winning Status'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_mix'),
                'bid_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Bid Id'
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('lof_auction_mix'),
                'is_auto',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'Is Auto'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $table = $installer->getTable('lof_auction_product');
            $history_table = $installer->getTable('lof_auction_history');
            $installer->getConnection()->addColumn(
                $table,
                'continue_time',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'If 2 customer bids are less than this time period, it will be considered continuous. Empty to use Module Settings'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'limit_bids',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Numbers of bids that customers can create in a short time. Empty to use Module Settings'
                ]
            );

            $installer->getConnection()->addColumn(
                $history_table,
                'customer_ip',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Customer IP address'
                ]
            );

            $installer->getConnection()->addColumn(
                $history_table,
                'customer_browser',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Customer Browser Info'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $mixAmountTable = $installer->getTable('lof_auction_mix');
            $installer->getConnection()->addColumn(
                $mixAmountTable,
                'updated_at',
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
                ]
            );
            $installer->getConnection()->addColumn(
                $mixAmountTable,
                'restrict_count',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => 1,
                    'comment' => 'Restrict Count for bidding'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            $mixAmountTable = $installer->getTable('lof_auction_mix');
            $installer->getConnection()->addColumn(
                $mixAmountTable,
                'is_subscribed',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'status of subscriber'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $newTable = $installer->getConnection()
                ->newTable($installer->getTable('lof_auction_absentee_bid'))
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Entity Id'
                )->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product Id'
                )->addColumn(
                    'auction_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Auction Id'
                )->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer Id'
                )->addColumn(
                    'max_absentee_amount',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Max Absentee Amount'
                )->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                    'status of record'
                )->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                    ],
                    'Created At'
                )->addColumn(
                    'updated_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
                    ],
                    'Updated At'
                )->addForeignKey(
                    $installer->getFkName('lof_auction_absentee_bid', 'customer_id', 'customer_entity', 'entity_id'),
                    'customer_id',
                    $installer->getTable('customer_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $installer->getFkName('lof_auction_absentee_bid', 'auction_id', 'lof_auction_product', 'entity_id'),
                    'auction_id',
                    $installer->getTable('lof_auction_product'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment('lof_auction_absentee_bid table');

            $installer->getConnection()->createTable($newTable);
        }
        if (version_compare($context->getVersion(), '1.0.9', '<')) {
            $productTable = $installer->getTable('lof_auction_product');
            $installer->getConnection()->addColumn(
                $productTable,
                'max_bids',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'Number of max bid for the product, the value use when enable check limit bid. Empty or input 0 for unlimited.'
                ]
            );
        }
        $setup->endSetup();
    }
}
