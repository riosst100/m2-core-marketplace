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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const LOF_QUICKRFQ_CART_TABLE = 'lof_quickrfq_cart';

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $quote_table = $setup->getTable(InstallSchema::LOF_QUICKRFQ_TABLE);

            $setup->getConnection()->addColumn(
                $quote_table,
                'product_sku',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Product Sku'
                ]
            );

            $setup->getConnection()->addColumn(
                $quote_table,
                'coupon_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Coupon Code'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'attributes',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Request Product Attributes Data'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'info_buy_request',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Buy Request Information'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'store_currency_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Currency Code'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'admin_quantity',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Quantity Admin Update'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'admin_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => "11,4",
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Price Admin Update'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'store_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 5,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Store ID'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'user_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 10,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Last Admin User ID'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'user_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 150,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Last Admin User Name'
                ]
            );
            $setup->getConnection()->addColumn(
                $quote_table,
                'expiry',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Expiry Date Time'
                ]
            );
            $setup->getConnection()->addForeignKey(
                $setup->getFkName(InstallSchema::LOF_QUICKRFQ_TABLE, 'store_id', 'store', 'store_id'),
                InstallSchema::LOF_QUICKRFQ_TABLE,
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
            //New Table lof_quickrfq_cart
            $quoteCartTable = $setup->getConnection()->newTable(
                $setup->getTable(self::LOF_QUICKRFQ_CART_TABLE)
            )
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'Entity ID'
                )
                ->addColumn(
                    'quickrfq_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Quote ID'
                )->addColumn(
                    'cart_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Cart ID - id of quote table'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                        'default' => 0
                    ],
                    'status. 0: is new, 1: created order, 2: expiry'
                )
                ->addColumn(
                    'expiry',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false],
                    'Expiry Date Time'
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                    'Created At'
                )->addForeignKey(
                    $setup->getFkName(self::LOF_QUICKRFQ_CART_TABLE, 'quickrfq_id', InstallSchema::LOF_QUICKRFQ_TABLE, 'quickrfq_id'),
                    'quickrfq_id',
                    $setup->getTable(InstallSchema::LOF_QUICKRFQ_TABLE),
                    'quickrfq_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $setup->getFkName(self::LOF_QUICKRFQ_CART_TABLE, 'cart_id', 'quote', 'entity_id'),
                    'quickrfq_id',
                    $setup->getTable('quote'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )
                ->setComment('Lof Quick Request For Quote - Cart');

            $setup->getConnection()->createTable($quoteCartTable);
        }
    }
}
