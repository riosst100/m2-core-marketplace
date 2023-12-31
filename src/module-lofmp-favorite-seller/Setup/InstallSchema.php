<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Setup;

use \Magento\Framework\DB\Ddl\Table;
use \Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_favoriteseller_subscription')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
            'Entity ID'
        )->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer ID'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT, ],
            'Creation Time'
        )->addForeignKey(
            $installer->getFkName('lofmp_favoriteseller_subscription', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
            'seller_id',
            $installer->getTable('lof_marketplace_seller'),
            'seller_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_favoriteseller_subscription', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                'lofmp_favoriteseller_subscription',
                ['seller_id', 'customer_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['seller_id', 'customer_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
