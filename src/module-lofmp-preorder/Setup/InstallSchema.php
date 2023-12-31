<?php


namespace Lofmp\PreOrder\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
        * Add field 'seller_id' in table 'lof_preorder_items'
        */
        $table = $installer->getTable('lof_preorder_items');

        $installer->getConnection()->addColumn(
            $table,
            'seller_id',
            [
                'type'     => Table::TYPE_INTEGER,
                'length'   => 11,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => 'Seller Id'
            ]
        );
        $installer->endSetup();
    }
}
