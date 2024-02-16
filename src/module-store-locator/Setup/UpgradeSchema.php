<?php

namespace CoreMarketplace\StoreLocator\Setup;

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

        if (version_compare($context->getVersion(), '1.0.2', '<')) 
        {
            $table = $installer->getTable('lofmp_storelocator_storelocator');

            $installer->getConnection()->addColumn(
                $table,
                'region',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Region'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'region_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => true,
                    'comment' => 'region id'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'address_line_1',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Address Line 1'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'address_line_2',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Address Line 2'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'postcode',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Postal Code'
                ]
            );

            $installer->getConnection()->addColumn(
                $table,
                'operating_hours',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Operating Hours'
                ]
            );
        }

        $installer->endSetup();
    }
}
