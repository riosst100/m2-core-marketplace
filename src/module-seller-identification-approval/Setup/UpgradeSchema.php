<?php

namespace CoreMarketplace\SellerIdentificationApproval\Setup;

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
                'verification_message_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Seller Verification Message Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) 
        {
            $sellerTable = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $sellerTable,
                'documents_verify_status',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 255,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Seller Documents Verify Status'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) 
        {
            $sellerTable = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $sellerTable,
                'disapproved_reason',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Seller Disapproved Reason'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) 
        {
            $sellerTable = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $sellerTable,
                'registration_step',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Seller Registration Step'
                ]
            );
        }

        $installer->endSetup();
    }
}
