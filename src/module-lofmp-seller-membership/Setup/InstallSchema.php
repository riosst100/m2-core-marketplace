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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$setup->getConnection()->isTableExists($installer->getTable('lofmp_marketplace_membership'))) {
            /*
            * Create table 'lofmp_marketplace_membership'
            */
            $table = $installer->getConnection()
                ->newTable($installer->getTable('lofmp_marketplace_membership'))
                ->addColumn(
                    'membership_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )
                ->addColumn(
                    'group_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'group Id'
                )
                ->addColumn(
                    'seller_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false],
                    'Seller Id'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'name'
                )
                ->addColumn(
                    'duration',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'duration'
                )
                ->addColumn(
                    'price',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Price'
                )
                ->addColumn(
                    'expiration_date',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    [],
                    'expiration date'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Created At'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )
                ->setComment('Marketplace membership Table');
            $installer->getConnection()->createTable($table);
        }

        if (!$setup->getConnection()->isTableExists($installer->getTable('lofmp_marketplace_membership_transaction'))) {
            /*
            * Create table 'ves_membership_transaction'
            */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lofmp_marketplace_membership_transaction')
            )->addColumn(
                'transaction_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Transaction Id'
            )->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Seller Id'
            )->addColumn(
                'package',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Package name'
            )->addColumn(
                'amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Transaction amount'
            )->addColumn(
                'duration',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Duration'
            )->addColumn(
                'duration_unit',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Duration Unit'
            )->addColumn(
                'additional_info',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                [],
                'Additional Info'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )->setComment(
                'Membership Transaction Table'
            );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
