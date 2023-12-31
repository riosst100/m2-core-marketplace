<?php

namespace Lofmp\Slider\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'lofmp_marketplace_slider'
         */
        $setup->getConnection()->dropTable($setup->getTable('lofmp_marketplace_slider'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_marketplace_slider')
        )->addColumn(
            'slider_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Slider Id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider title'
        )->addColumn(
            'effect',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Slider effect'
        )->addColumn(
            'pagination',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'enabled or disabled pagination'
        )->addColumn(
            'image_height',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Image height'
        )->addColumn(
            'image_width',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Image width'
        )->addColumn(
            'thumbnail',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'enabled or disabled thumbnail'
        )->addColumn(
            'slider_speed',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'slider speed'
        )->addColumn(
            'slider_duration',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'Slider duration'
        )->addColumn(
            'image_url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Slider title'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'seller id'
        )->addColumn(
            'image_type',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Status'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            4,
            ['nullable' => true],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Slider Creation Time'
        )->addIndex(
            $setup->getIdxName('lofmp_marketplace_slider', ['slider_id']),
            ['slider_id']
            );
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}