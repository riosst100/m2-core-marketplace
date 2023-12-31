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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        /**
         *  setup for Seller Settings
         */
        if (version_compare($context->getVersion(), '1.0.12', '<')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lof_marketplace_seller_settings')
            )->addColumn(
                'setting_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Setting Id'
            )->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Seller Id'
            )->addColumn(
                'group',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '32',
                ['nullable' => false],
                'Group'
            )->addColumn(
                'key',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64',
                ['nullable' => false],
                'Key'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '500',
                ['nullable' => false],
                'value'
            )->addColumn(
                'serialized',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'serialized'
            )->setComment(
                'MarketPlace Seller Settings'
            );
            $installer->getConnection()->createTable($table);

            /**
             *  lof_marketplace_message_admin
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lof_marketplace_message_admin')
            )->addColumn(
                'message_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )->addColumn(
                'identifier',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [],
                'Identifier'
            )->addColumn(
                'admin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Owner Id'
            )->addColumn(
                'admin_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Admin Email'
            )->addColumn(
                'admin_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Admin Name'
            )->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sender Id'
            )->addColumn(
                'seller_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Sender Email'
            )->addColumn(
                'seller_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Sender Name'
            )->addColumn(
                'seller_send',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Seller Send'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                [],
                'Description'
            )->addColumn(
                'receiver_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Receiver Id'
            )->addColumn(
                'subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Subject'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false],
                'Status (0 => Draft, 1 => Unread, 2 => Read, 3 => Sent)'
            )->addColumn(
                'is_read',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false],
                'Is read Message (0 => No, 1 => Yes)'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            );
            $installer->getConnection()->createTable($table);

            /* table lof_marketplace_message_detail */
            $table = $installer->getTable('lof_marketplace_message_detail');

            $installer->getConnection()->addColumn(
                $table,
                'message_admin',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'message admin'
                ]
            );

            /* table lof_marketplace_seller */
            $table = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $table,
                'page_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'page id facebook'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'country_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'country_id'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'verify_status',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 15,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'verify_status'
                ]
            );
            /* table lof_marketplace_group */
            $table = $installer->getTable('lof_marketplace_group');

            $installer->getConnection()->addColumn(
                $table,
                'limit_product',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => true,
                    'comment' => 'Limit Product'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_add_product',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Can add product'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_cancel_order',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Can cancel order'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_create_invoice',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_create_invoice'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_create_shipment',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_create_shipment'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_create_creditmemo',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_create_creditmemo'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'hide_payment_info',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'hide_payment_info'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'hide_customer_email',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'hide_customer_email'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_shipping',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_shipping'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_submit_order_comments',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_submit_order_comments'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_message',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_message'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_review',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_review'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_rating',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_rating'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_import_export',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_import_export'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_vacation',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_vacation'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_report',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_report'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'can_use_withdrawal',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'can_use_withdrawal'
                ]
            );
            /* table lof_marketplace_payment */
            $table = $installer->getTable('lof_marketplace_payment');
            $installer->getConnection()->addColumn(
                $table,
                'fee_by',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Fee By'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'fee_percent',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => true,
                    'comment' => 'Fixed Percent'
                ]
            );

            $table = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->addColumn(
                $table,
                'company',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Company'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'city',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'City'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'region',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Region'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'street',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'street'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'product_count',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => true,
                    'comment' => 'Product count'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'duration_of_vendor',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => true,
                    'comment' => 'duration_of_vendor'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'region_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => true,
                    'comment' => 'region id'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'postcode',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 15,
                    'nullable' => true,
                    'comment' => 'postcode'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'telephone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 50,
                    'nullable' => true,
                    'comment' => 'Telephone'
                ]
            );
            $installer->getConnection()->addColumn(
                $table,
                'total_sold',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => '10,4',
                    'nullable' => true,
                    'comment' => 'total amount sold'
                ]
            );
            $table = $installer->getTable('lof_marketplace_seller');

            $installer->getConnection()->modifyColumn(
                $table,
                'country_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 15,
                    'nullable' => false,
                    'comment' => 'country_id'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.13', '<')) {
            $table = $installer->getTable('lof_marketplace_seller');
            $installer->getConnection()->modifyColumn(
                $table,
                'postcode',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 15,
                    'nullable' => false,
                    'comment' => 'postcode'
                ]
            );
        }

        $table_lof_marketplace_seller_settings = $installer->getTable("lof_marketplace_seller_settings");

        if (version_compare($context->getVersion(), '1.0.14', '<')) {
            $installer->getConnection()->addColumn(
                $table_lof_marketplace_seller_settings,
                'scope',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 8,
                    'nullable' => false,
                    'default' => 'default',
                    'comment' => 'Resource ID'
                ]
            );

            $installer->getConnection()->addColumn(
                $table_lof_marketplace_seller_settings,
                'scope_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Config Scope ID'
                ]
            );

            $installer->getConnection()->addColumn(
                $table_lof_marketplace_seller_settings,
                'path',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => 'general',
                    'comment' => 'Config Path'
                ]
            );

            $installer->getConnection()->addColumn(
                $table_lof_marketplace_seller_settings,
                'value',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '1M',
                    'nullable' => true,
                    'comment' => 'Config Value'
                ]
            );

            $installer->getConnection()->addColumn(
                $table_lof_marketplace_seller_settings,
                'updated_at',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                    'comment' => 'Updated At'
                ]
            );

            $installer->getConnection()->addIndex(
                $table_lof_marketplace_seller_settings,
                $installer->getIdxName($table_lof_marketplace_seller_settings, ['seller_id']),
                ['seller_id']
            );

            $installer->getConnection()->addIndex(
                $table_lof_marketplace_seller_settings,
                $installer->getIdxName($table_lof_marketplace_seller_settings, ['scope']),
                ['scope']
            );

            $installer->getConnection()->addIndex(
                $table_lof_marketplace_seller_settings,
                $installer->getIdxName($table_lof_marketplace_seller_settings, ['scope_id']),
                ['scope_id']
            );

            $installer->getConnection()->addIndex(
                $table_lof_marketplace_seller_settings,
                $installer->getIdxName($table_lof_marketplace_seller_settings, ['path']),
                ['path']
            );

            $installer->getConnection()->addForeignKey(
                $installer->getFkName('lof_marketplace_seller_settings', 'seller_id', 'seller', 'seller_id'),
                'lof_marketplace_seller_settings',
                'seller_id',
                $installer->getTable('lof_marketplace_seller'),
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );
        }

        if (version_compare($context->getVersion(), '1.0.15', '<')) {
            $table = $installer->getTable('lof_marketplace_seller');
            $installer->getConnection()->modifyColumn(
                $table,
                'telephone',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => true,
                    'comment' => 'Telephone'
                ]
            );
            $installer->getConnection()->addColumn(
                $table_lof_marketplace_seller_settings,
                'scope_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Config Scope ID'
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.16', '<')) {
            $this->addSellerOrderSellerShippingAmountColumn($installer);
        }

        if (version_compare($context->getVersion(), '1.0.17', '<')) {
            $this->addVacationTextShippingColumn($installer);
        }

        if (version_compare($context->getVersion(), '1.0.18', '<')) {
            $this->addShippingCommissionTables($installer);
        }

        if (version_compare($context->getVersion(), '1.0.19', '<')) {
            $this->deleteGroupIdForeignKey($installer);
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->modifyWithdrawalColumnType($installer);
        }

        if (version_compare($context->getVersion(), '1.1.1', '<')) {
            $this->addSellerCompanyUrlColumn($installer);
        }

        if (version_compare($context->getVersion(), '1.1.2', '<')) {
            $this->createRatingSummary($installer);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $this->updateSellerRating($installer);
        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $this->addNewFields($installer);
        }

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addColumnSellerIdToItems($installer);
        }

        if (version_compare($context->getVersion(), '1.2.1', '<')) {
            $this->addColumnStatusToAmountTransaction($installer);
        }

        $installer->endSetup();
    }

    /**
     * Add column lof_seller_id to quote items and order items
     * 
     * @param \Magento\Framework\Setup\SchemaSetupInterface $installer
     */
    protected function addColumnSellerIdToItems($installer)
    {
        $table = $installer->getTable('quote_item');
        $installer->getConnection()->addColumn(
            $table,
            'lof_seller_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'length' => 10,
                'unsigned' => true,
                'comment' => 'Lof marketplace seller id',
            ]
        );

        $table = $installer->getTable('sales_order_item');
        $installer->getConnection()->addColumn(
            $table,
            'lof_seller_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'length' => 10,
                'unsigned' => true,
                'comment' => 'Lof marketplace seller id',
            ]
        );

    }

    /**
     * @param $installer
     */
    protected function addSellerOrderSellerShippingAmountColumn($installer)
    {
        $table = $installer->getTable('lof_marketplace_sellerorder');
        $installer->getConnection()->addColumn(
            $table,
            'seller_shipping_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'nullable' => true,
                'length' => '12,4',
                'comment' => 'Seller Shipping Amount',
                'after' => 'shipping_amount'
            ]
        );
    }

    /**
     * @param $installer
     */
    protected function deleteGroupIdForeignKey($installer)
    {
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('lof_marketplace_seller'),
            $installer->getFkName(
                'lof_marketplace_seller',
                'group_id',
                'lof_marketplace_seller',
                'group_id'
            )
        );
    }

    /**
     * @param $installer
     */
    protected function addVacationTextShippingColumn($installer)
    {
        $table = $installer->getTable('lof_marketplace_vacation');
        $installer->getConnection()->addColumn(
            $table,
            'text_shipping_method',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Text Shipping Info'
            ]
        );
    }

    /**
     * @param $installer
     */
    protected function addShippingCommissionTables($installer)
    {
        $setup = $installer;
        /**
         * Create table 'lof_marketplace_shipping_commission'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_shipping_commission'))
            ->addColumn(
                'shipping_commission_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Shipping Commission ID'
            )
            ->addColumn(
                'commission_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Commission Title'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'group_id',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Group ID'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\DB\Ddl\Table::DEFAULT_TEXT_SIZE,
                ['nullable' => false],
                'Description'
            )
            ->addColumn(
                'from_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => true],
                'From Date'
            )->addColumn(
                'to_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                null,
                ['nullable' => true],
                'To Date'
            )
            ->addColumn(
                'priority',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'Prioirity'
            )->addColumn(
                'commission_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                ['nullable' => true],
                'Commission By'
            )->addColumn(
                'commission_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => true],
                'Commission Acmount'
            )
            ->addColumn(
                'create_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'create_at'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                1,
                ['nullable' => false, 'default' => '1'],
                'Active'
            );
        $setup->getConnection()->createTable($table);
        /**
         * Create table 'lof_marketplace_shipping_commission_store'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_shipping_commission_store'))
            ->addColumn(
                'shipping_commission_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Seller Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store Id'
            )
            ->addIndex(
                $setup->getIdxName('lof_marketplace_shipping_commission_store', ['store_id']),
                ['store_id']
            );
        $setup->getConnection()->createTable($table);
        /**
         * Create table 'lof_marketplace_shipping_commission_group'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_shipping_commission_group'))
            ->addColumn(
                'shipping_commission_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Shipping Commission Id'
            )
            ->addColumn(
                'group_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Group Id'
            )
            ->addIndex(
                $setup->getIdxName('lof_marketplace_shipping_commission_group', ['group_id']),
                ['group_id']
            );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param $installer
     */
    protected function createRatingSummary($installer)
    {
        $setup = $installer;
        /**
         * Create table 'lof_marketplace_review_summary'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_review_summary'))
            ->addColumn(
                'primary_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Primary Id'
            )
            ->addColumn(
                'rating_type',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'rating_type: 1 - Quality, 2 - Value, 3 - Price, 4 - Rating, 5 - Support, 6 - Shipping'
            )
            ->addColumn(
                'reviews_count',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'reviews_count, total count'
            )
            ->addColumn(
                'rating_summary',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'rating_summary, total count'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'rate_one',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'rating one'
            )
            ->addColumn(
                'rate_two',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'rating two'
            )
            ->addColumn(
                'rate_three',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'rating three'
            )
            ->addColumn(
                'rate_four',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'rating four'
            )
            ->addColumn(
                'rate_five',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'rating five'
            )
            ->addIndex(
                $setup->getIdxName('lof_marketplace_review_summary', ['store_id', 'rating_type']),
                ['store_id', 'rating_type']
            );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param $installer
     */
    public function modifyWithdrawalColumnType($installer)
    {
        $setup = $installer;
        $withdrawalTable = $setup->getTable('lof_marketplace_withdrawal');
        if ($setup->getConnection()->isTableExists($withdrawalTable)) {
            foreach (['fee', 'amount', 'net_amount'] as $column) {
                if ($setup->getConnection()->tableColumnExists($withdrawalTable, $column)) {
                    $setup->getConnection()->modifyColumn(
                        $withdrawalTable,
                        $column,
                        [
                            'type' => Table::TYPE_DECIMAL,
                            'length' => '12,4',
                            'comment' => ucfirst($column)
                        ]
                    );
                }
            }
        }
    }

    /**
     * @param $installer
     */
    protected function addSellerCompanyUrlColumn($installer)
    {
        $table = $installer->getTable('lof_marketplace_seller');
        $installer->getConnection()->addColumn(
            $table,
            'company_url',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Company URL',
                'after' => 'company'
            ]
        );
    }

    /**
     * @param $installer
     */
    protected function addColumnStatusToAmountTransaction($installer)
    {
        $table = $installer->getTable('lof_marketplace_amount_transaction');
        $installer->getConnection()->addColumn(
            $table,
            'status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Status',
                'default' => 1
            ]
        );
    }

    /**
     * @param $installer
     */
    protected function updateSellerRating($installer)
    {
        $table = $installer->getTable('lof_marketplace_rating');
        $installer->getConnection()->addColumn(
            $table,
            'like_about',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Like About',
                'after' => 'nickname'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'not_like_about',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Not Like About',
                'after' => 'like_about'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'plus_review',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Plus Review',
                'after' => 'not_like_about'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'minus_review',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Minus Review',
                'after' => 'plus_review'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'report_abuse',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Report Abuse',
                'after' => 'minus_review'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'verified_buyer',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is verified buyer 0 - no, 1 - yes',
                'after' => 'report_abuse'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'is_recommended',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is recommended 0 - no, 1 - yes',
                'after' => 'verified_buyer'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'is_hidden',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is hidden 0 - no, 1 - yes',
                'after' => 'is_recommended'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'answer',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Seller Answer',
                'after' => 'is_hidden'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'admin_note',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Admin Note',
                'after' => 'answer'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'country',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Country Id',
                'after' => 'admin_note'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'updated_at',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                'nullable' => true,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE,
                'comment' => 'Updated At',
                'after' => 'created_at'
            ]
        );
    }

    /**
     * @param $installer
     */
    protected function addNewFields($installer)
    {
        $table = $installer->getTable('lof_marketplace_seller');
        $installer->getConnection()->addColumn(
            $table,
            'operating_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 50,
                'comment' => 'Operating Time',
                'after' => 'company'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'order_processing_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 50,
                'comment' => 'Order Processing Time',
                'after' => 'operating_time'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'shipping_partners',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Shipping Partners',
                'after' => 'order_processing_time'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'offers',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 250,
                'comment' => 'Seller Offers',
                'after' => 'shipping_partners'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'benefits',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 250,
                'comment' => 'Seller Benefits',
                'after' => 'offers'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'product_shipping_info',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 250,
                'comment' => 'Seller Product Shipping Information',
                'after' => 'benefits'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'prepare_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Prepare Time',
                'after' => 'product_shipping_info'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'response_ratio',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Response Ratio',
                'after' => 'prepare_time'
            ]
        );
        $installer->getConnection()->addColumn(
            $table,
            'response_time',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 50,
                'nullable' => true,
                'comment' => 'Response Time',
                'after' => 'response_ratio'
            ]
        );
    }
}
