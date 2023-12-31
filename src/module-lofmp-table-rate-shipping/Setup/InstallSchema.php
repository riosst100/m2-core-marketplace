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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'lofmp_marketplace_shippinglist'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lofmp_marketplace_shippinglist'))
            ->addColumn(
                'lofmpshipping_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Lof TableRateShipping ID'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Website ID'
            )
            ->addColumn(
                'dest_country_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => ''],
                'Destination coutry ISO/2 or ISO/3 code'
            )
            ->addColumn(
                'dest_region_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => ''],
                'Destination Region Id'
            )
            ->addColumn(
                'dest_zip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => ''],
                'Destination Post Code (Zip)'
            )
            ->addColumn(
                'dest_zip_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => ''],
                'Destination Post Code (Zip)'
            )
            ->addColumn(
                'price',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['unsigned' => true, 'nullable' => false, 'default' => '0.0000'],
                'Price'
            )
            ->addColumn(
                'weight_from',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['unsigned' => true, 'nullable' => false, 'default' => '0.0000'],
                'weight_from'
            )
            ->addColumn(
                'weight_to',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['unsigned' => true, 'nullable' => false, 'default' => '0.0000'],
                'Weight to'
            )
            ->addColumn(
                'partner_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Partner Id'
            )
            ->addColumn(
                'shipping_method_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Shipping Method Id'
            )->setComment('Marketplace Table rate shipping table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('lofmp_marketplace_shippinglist_method'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Index ID'
            )
            ->addColumn(
                'method_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => ''],
                'Shipping Method name'
            )
            ->addColumn(
                'partner_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' =>  '0'],
                'Partner ID'
            )
            ->setComment('Marketplace Table rate shipping methods table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
