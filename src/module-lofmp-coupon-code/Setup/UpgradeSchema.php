<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2016 Venustheme (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.3', '<=')) {
            /**
             * Create table 'lofmp_coupon_code_log'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lofmp_coupon_code_log')
            )->addColumn(
                'log_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'log id'
            )->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Rule Id'
            )->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'Order id'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Customer id'
            )
            ->addColumn(
                'full_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Customer full name'
            )->addColumn(
                'generated_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'generated qrcode link'
            )->addColumn(
                'email_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Email address '
            )->addColumn(
                'coupon_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'Coupon code'
            )->addColumn(
                'discount_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                [12, 4],
                ['nullable' => true, 'default' => '0.0000'],
                'Discount Amount'
            )->addColumn(
                'coupon_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true, 'default' => '1'],
                'Coupon Type'
            )->addColumn(
                'ip_address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'ip address'
            )->addColumn(
                'client_info',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Client info'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                255,
                ['nullable' => true, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Created time'
            )->addIndex(
                    $setup->getIdxName('lofmp_coupon_code_log', ['log_id']),
                    ['log_id']
                    );
            $installer->getConnection()->createTable($table);

        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            if ($setup->getConnection()->isTableExists('lofmp_couponcode_rule')) {
                $setup->getConnection()->changeColumn(
                    $setup->getTable('lofmp_couponcode_rule'),
                    'rule_id',
                    'rule_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'unsigned' => true
                    ]
                );

                $setup->getConnection()->changeColumn(
                    $setup->getTable('lofmp_couponcode_rule'),
                    'seller_id',
                    'seller_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true
                    ]
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_couponcode_rule', 'rule_id', 'salesrule', 'rule_id'),
                    'lofmp_couponcode_rule',
                    'rule_id',
                    $setup->getTable('salesrule'),
                    'rule_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_couponcode_rule', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
                    'lofmp_couponcode_rule',
                    'seller_id',
                    $setup->getTable('lof_marketplace_seller'),
                    'seller_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }

            if ($setup->getConnection()->isTableExists('lofmp_couponcode_coupon')) {
                $setup->getConnection()->changeColumn(
                    $setup->getTable('lofmp_couponcode_coupon'),
                    'coupon_id',
                    'coupon_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'unsigned' => true
                    ]
                );

                $setup->getConnection()->changeColumn(
                    $setup->getTable('lofmp_couponcode_coupon'),
                    'seller_id',
                    'seller_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'unsigned' => true
                    ]
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_couponcode_coupon', 'coupon_id', 'salesrule_coupon', 'coupon_id'),
                    'lofmp_couponcode_coupon',
                    'coupon_id',
                    $setup->getTable('salesrule_coupon'),
                    'coupon_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );

                $setup->getConnection()->addForeignKey(
                    $setup->getFkName('lofmp_couponcode_coupon', 'seller_id', 'lof_marketplace_seller', 'seller_id'),
                    'lofmp_couponcode_coupon',
                    'seller_id',
                    $setup->getTable('lof_marketplace_seller'),
                    'seller_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            }
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('lofmp_couponcode_coupon'),
                'is_public',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 5,
                    'nullable' => true,
                    'default' => 0,
                    'comment' => 'is public coupon, 1 - Yes, 0 - No',
                ]
            );
        }
        $installer->endSetup();
    }
}
