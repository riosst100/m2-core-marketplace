<?php

namespace CoreMarketplace\MarketPlace\Setup;

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

        if (version_compare($context->getVersion(), '1.0.1', '<')) 
        {
            $sellerTable = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $sellerTable,
                'address_line_1',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Seller Address Line 1'
                ]
            );

            $installer->getConnection()->addColumn(
                $sellerTable,
                'address_line_2',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Seller Address Line 2'
                ]
            );

            $installer->getConnection()->addColumn(
                $sellerTable,
                'company_registration_number',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Company Registration Number'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) 
        {
            $sellerTable = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $sellerTable,
                'term_and_conditions',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Term & Conditions'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) 
        {
            $sellerTable = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $sellerTable,
                'seller_type',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Seller Type'
                ]
            );
        }

        $installer->endSetup();
    }
}
