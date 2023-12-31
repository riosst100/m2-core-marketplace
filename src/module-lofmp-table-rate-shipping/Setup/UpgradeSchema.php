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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $tableName = $installer->getTable('lofmp_marketplace_shippinglist');
            $fullTextIntex = [
                'dest_country_id',
                'dest_region_id'
            ]; // Column with fulltext index, you can put multiple fields

            $installer->getConnection()->addIndex(
                $tableName,
                $installer->getIdxName(
                    $tableName,
                    $fullTextIntex,
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                $fullTextIntex,
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $table = $installer->getTable('lofmp_marketplace_shippinglist');
            $installer->getConnection()->addColumn(
                $table,
                'free_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'size' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'free_shipping'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $table = $installer->getTable('lofmp_marketplace_shippinglist');
            $installer->getConnection()->addColumn(
                $table,
                'cost',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'size' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'Shipping Cost'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'cart_total',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'size' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'Rate Condition Minimum Cart Total Amount Condition. empty or set 0 to ignore it.'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $table = $installer->getTable('lofmp_marketplace_shippinglist');
            $installer->getConnection()->modifyColumn(
                $table,
                'free_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                ]
            );

            $installer->getConnection()->modifyColumn(
                $table,
                'cart_total',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                ]
            );
        }
    }
}
