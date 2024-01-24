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

        $installer->endSetup();
    }
}
