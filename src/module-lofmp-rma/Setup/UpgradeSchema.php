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
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2019 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.7', '<')) {
            /** Add new columns for table lofmp_rma_rma */
            $lofmp_rma_rma_table = $installer->getTable('lofmp_rma_rma');
            $installer->getConnection()->addColumn(
                $lofmp_rma_rma_table,
                'parent_rma_id',
                [
                    'type'     => Table::TYPE_INTEGER,
                    'length'   => 11,
                    'default'  => '0',
                    'unsigned' => false,
                    'comment'  => 'parent rma id, default: 0'
                ]
            );
            $installer->getConnection()->addColumn(
                $lofmp_rma_rma_table,
                'customer_email',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 150,
                    'default'  => '',
                    'comment'  => 'customer email to filter rma by guest'
                ]
            );
            /** Create new database table lofmp_rma_rma_track */
            $lofmp_rma_rma_track_table = $installer->getConnection()->newTable(
                $installer->getTable('lofmp_rma_rma_track')
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Track Entity Id'
            )->addColumn(
                'parent_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Parent id'
            )->addColumn(
                'weight',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Weight'
            )->addColumn(
                'qty',
                Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'QTY'
            )->addColumn(
                'rma_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'RMA Id'
            )->addColumn(
                'seller_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Seller Id'
            )->addColumn(
                'track_number',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Return Tracking Number'
            )->addColumn(
                'description',
                Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Description'
            )->addColumn(
                'title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Title'
            )->addColumn(
                'carrier_code',
                Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'Carrier Code'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Created At'
            )->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            );
            $installer->getConnection()->createTable($lofmp_rma_rma_track_table);

            /** Add new column for table  */
            $lofmp_rma_message_table = $installer->getTable('lofmp_rma_message');
            $installer->getConnection()->addColumn(
                $lofmp_rma_message_table,
                'customer_email',
                [
                    'type'     => Table::TYPE_TEXT,
                    'length'   => 150,
                    'default'  => '',
                    'comment'  => 'customer email to filter rma by guest'
                ]
            );

            /** Create new table lofmp_rma_return_address_table */
            $lofmp_rma_return_address_table = $installer->getConnection()->newTable(
                $installer->getTable('lofmp_rma_return_address')
            )->addColumn(
                'address_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Return Address Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                128,
                ['unsigned' => false, 'nullable' => false],
                'Return Address'
            )->addColumn(
                'address',
                Table::TYPE_TEXT,
                512,
                ['unsigned' => false, 'nullable' => false],
                'Return Address'
            )->addColumn(
                'seller_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Seller Id'
            )->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Sort Order'
            )->addColumn(
                'is_active',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'default' => 0],
                'Is Active'
            );
            $installer->getConnection()->createTable($lofmp_rma_return_address_table);
        }
        $installer->endSetup();
    }
}
