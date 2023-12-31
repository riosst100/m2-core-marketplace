<?php
namespace Lofmp\Quickrfq\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                $setup->getTable('lof_quickrfq'),
                'seller_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => null,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Seller Id'
                ]
            );
            $connection->addColumn(
                $setup->getTable('lof_quickrfq'),
                'seller_name',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 250,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Seller Name'
                ]
            );
        }
    }
}
