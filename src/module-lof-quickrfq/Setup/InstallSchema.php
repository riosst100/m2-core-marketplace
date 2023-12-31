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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Lof\Quickrfq\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     *
     */
    const LOF_QUICKRFQ_TABLE = 'lof_quickrfq';
    /**
     *
     */
    const LOF_ATTACHMENT_TABLE = 'lof_quickrfq_attachment';
    /**
     *
     */
    const LOF_MESSAGE_TABLE = 'lof_quickrfq_message';


    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();


        /**
         * Create table 'quickrfq'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable(self::LOF_QUICKRFQ_TABLE))
            ->addColumn(
                'quickrfq_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quickrfq ID'
            )
            ->addColumn(
                'contact_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Contact Name'
            )
            ->addColumn(
                'phone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Phone'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Email'
            )
            ->addColumn(
                'comment',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Comment'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'Product Id'
            )
            ->addColumn(
                'quantity',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'Quantity Request'
            )->addColumn(
                'price_per_product',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                [11, 4],
                [],
                'Price Per Product'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => 'New'],
                'Status'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [],
                'Customer Id'
            )
            ->addColumn(
                'date_need_quote',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Date Need Quote'
            )
            ->addColumn(
                'create_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Date'
            )
            ->addColumn(
                'update_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Date'
            );
        $installer->getConnection()->createTable($table);

        if (!$installer->tableExists(self::LOF_ATTACHMENT_TABLE)) {
            $quoteAttachmentTable = $installer->getConnection()->newTable(
                $installer->getTable(self::LOF_ATTACHMENT_TABLE)
            )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'quickrfq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Quote ID'
                )->addColumn(
                    'message_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Message ID'
                )
                ->addColumn(
                    'file_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                    ],
                    'File Name'
                )
                ->addColumn(
                    'file_path',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'File Path'
                )
                ->addColumn(
                    'file_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    32,
                    [
                        'nullable' => true,
                    ],
                    'File Type'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Created At'
                )->addForeignKey(
                    $installer->getFkName(self::LOF_ATTACHMENT_TABLE, 'quickrfq_id', self::LOF_QUICKRFQ_TABLE, 'quickrfq_id'),
                    'quickrfq_id',
                    $installer->getTable(self::LOF_QUICKRFQ_TABLE),
                    'quickrfq_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Lof Quick Request For Quote Attachment');

            $installer->getConnection()->createTable($quoteAttachmentTable);
        }
        if (!$installer->tableExists(self::LOF_MESSAGE_TABLE)) {
            $quoteMessageTable = $installer->getConnection()->newTable(
                $installer->getTable(self::LOF_MESSAGE_TABLE)
            )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'quickrfq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Quote ID'
                )->addColumn(
                    'customer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Customer ID'
                )
                ->addColumn(
                    'message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    '64k',
                    [
                        'nullable' => false,
                    ],
                    'Message'
                )
                ->addColumn(
                    'is_main',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                        'unsigned' => true,
                    ],
                    'Is Main'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Created At'
                )->addForeignKey(
                    $installer->getFkName(self::LOF_MESSAGE_TABLE, 'quickrfq_id', self::LOF_QUICKRFQ_TABLE, 'quickrfq_id'),
                    'quickrfq_id',
                    $installer->getTable(self::LOF_QUICKRFQ_TABLE),
                    'quickrfq_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Lof Quick Request For Message');
            $installer->getConnection()->createTable($quoteMessageTable);
        }
        $installer->endSetup();
    }
}
