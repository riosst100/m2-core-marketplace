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
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\MultiShipping\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addChargeTransferToColumnInOrderTable($setup);
            $this->modifyShippingMethodColumnType($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addMpInfoColumn($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addChargeTransferToColumnInOrderTable(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $salesOrderTable = $setup->getTable('sales_order');

        if ($connection->isTableExists($salesOrderTable)) {
            $connection->addColumn(
                $salesOrderTable,
                'charge_transfer_to',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => true,
                    'comment' => 'Charge Transfer to - Admin/Seller'
                ],
                null
            );
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function modifyShippingMethodColumnType(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $column = 'shipping_method';
        $tables = ['quote_address', 'sales_order'];

        foreach ($tables as $table) {
            $tableName = $setup->getTable($table);
            if ($connection->isTableExists($tableName)) {
                if ($connection->tableColumnExists($tableName, $column)) {
                    $connection->modifyColumn(
                        $tableName,
                        $column,
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'nullable' => true
                        ]
                    );
                }
            }
        }

        $quoteShippingRateTable = $setup->getTable('quote_shipping_rate');
        if ($connection->isTableExists($quoteShippingRateTable)) {
            foreach (['code', 'method'] as $column) {
                if ($connection->tableColumnExists($quoteShippingRateTable, $column)) {
                    $connection->modifyColumn(
                        $quoteShippingRateTable,
                        $column,
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => ucfirst($column)
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addMpInfoColumn(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tables = ['quote_shipping_rate', 'sales_order'];
        foreach ($tables as $table) {
            $tableName = $setup->getTable($table);
            if ($connection->isTableExists($tableName)) {
                if (!$connection->tableColumnExists($tableName, 'mp_info')) {
                    $connection->addColumn(
                        $tableName,
                        'mp_info',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'nullable' => true,
                            'comment' => 'MarketPlace Multishipping Info'
                        ]
                    );
                }
            }
        }
    }
}
