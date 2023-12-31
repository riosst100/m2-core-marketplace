<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     *
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        
       
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_attachment')
        )->addColumn(
            'attachment_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Message Id'
        )->addColumn(
            'item_type',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Item Type'
        )->addColumn(
            'item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Item Id'
        )->addColumn(
            'uid',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'UID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'name'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true],
            'type'
        )->addColumn(
            'size',
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => false, 'nullable' => true],
            'size'
        )->addColumn(
            'body',
            Table::TYPE_BLOB,
            '4G',
            ['unsigned' => false, 'nullable' => false],
            'body'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_message')
        )->addColumn(
            'message_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Message Id'
        )->addColumn(
            'rma_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Rma Id'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'User Id'
        )
        ->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Seller Id'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Customer Id'
        )->addColumn(
            'customer_name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Customer Name'
        )->addColumn(
            'text',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Text'
        )->addColumn(
            'is_html',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Html'
        )->addColumn(
            'internal',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Internal'
        )->addColumn(
            'is_customer_notified',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Customer Notified'
        )->addColumn(
            'status_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Status Id'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Updated At'
        )->addColumn(
            'email_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Email Id'
        )->addColumn(
            'is_read',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Read'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_message', ['rma_id']),
            ['rma_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_message', ['user_id']),
            ['user_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_message', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_message', ['status_id']),
            ['status_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_message',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_message',
                'user_id',
                'admin_user',
                'user_id'
            ),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_message',
                'rma_id',
                'lofmp_rma_rma',
                'rma_id'
            ),
            'rma_id',
            $installer->getTable('lofmp_rma_rma'),
            'rma_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_message',
                'status_id',
                'lofmp_rma_status',
                'status_id'
            ),
            'status_id',
            $installer->getTable('lofmp_rma_status'),
            'status_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_condition')
        )->addColumn(
            'condition_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Condition Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_field')
        )->addColumn(
            'field_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Field Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Type'
        )->addColumn(
            'values',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Values'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Description'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'is_required_staff',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Required Staff'
        )->addColumn(
            'is_required_customer',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Required Customer'
        )->addColumn(
            'is_visible_customer',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Visible Customer'
        )->addColumn(
            'is_editable_customer',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Editable Customer'
        )
        ->addColumn(
            'visible_customer_status',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Visible Customer Status'
        )->addColumn(
            'is_show_in_confirm_shipping',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Show In Confirm Shipping'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_item')
        )->addColumn(
            'item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Item Id'
        )->addColumn(
            'rma_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Rma Id'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Product Id'
        )->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Order Item Id'
        )->addColumn(
            'reason_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Reason Id'
        )->addColumn(
            'resolution_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Resolution Id'
        )->addColumn(
            'condition_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Condition Id'
        )->addColumn(
            'qty_requested',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Qty Requested'
        )->addColumn(
            'qty_returned',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Qty Returned'
        )->addColumn(
            'admin_commission',
            Table::TYPE_DECIMAL,
            '12,4',
            [ ],
            'Admin Commission'
        )
        ->addColumn(
            'seller_commission',
            Table::TYPE_DECIMAL,
            '12,4',
            [ ],
            'Admin Commission'
        )
        ->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_item', ['rma_id']),
            ['rma_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_item', ['reason_id']),
            ['reason_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_item', ['resolution_id']),
            ['resolution_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_item', ['condition_id']),
            ['condition_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_item',
                'condition_id',
                'lofmp_rma_condition',
                'condition_id'
            ),
            'condition_id',
            $installer->getTable('lofmp_rma_condition'),
            'condition_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_item',
                'reason_id',
                'lofmp_rma_reason',
                'reason_id'
            ),
            'reason_id',
            $installer->getTable('lofmp_rma_reason'),
            'reason_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_item',
                'rma_id',
                'lofmp_rma_rma',
                'rma_id'
            ),
            'rma_id',
            $installer->getTable('lofmp_rma_rma'),
            'rma_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_item',
                'resolution_id',
                'lofmp_rma_resolution',
                'resolution_id'
            ),
            'resolution_id',
            $installer->getTable('lofmp_rma_resolution'),
            'resolution_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_reason')
        )->addColumn(
            'reason_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Reason Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_resolution')
        )->addColumn(
            'resolution_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Resolution Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code'
        );
        $installer->getConnection()->createTable($table);
         

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_rma')
        )->addColumn(
            'rma_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Rma Id'
        )->addColumn(
            'increment_id',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Increment Id'
        )->addColumn(
            'return_address',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Return address'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Customer Id'
        )->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'Seller Id'
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'status_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Status Id'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Created At'
        )->addColumn(
            'updated_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Updated At'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'User Id'
        )->addColumn(
            'last_reply_name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Last Reply Name'
        )->addColumn(
            'last_reply_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Last Reply At'
        )->addColumn(
            'is_admin_read',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Admin Read'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma', ['order_id']),
            ['order_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma', ['status_id']),
            ['status_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma',
                'customer_id',
                'customer_entity',
                'entity_id'
            ),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma',
                'order_id',
                'sales_order',
                'entity_id'
            ),
            'order_id',
            $installer->getTable('sales_order'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma',
                'status_id',
                'lofmp_rma_status',
                'status_id'
            ),
            'status_id',
            $installer->getTable('lofmp_rma_status'),
            'status_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma',
                'store_id',
                'store',
                'store_id'
            ),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName(
                'lofmp_rma_rma',
                ['increment_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['increment_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_rma_creditmemo')
        )->addColumn(
            'rma_creditmemo_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Rma Creditmemo Id'
        )->addColumn(
            'rc_rma_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Rc Rma Id'
        )->addColumn(
            'rc_credit_memo_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Rc Credit Memo Id'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma_creditmemo', ['rc_rma_id']),
            ['rc_rma_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma_creditmemo',
                'rc_rma_id',
                'lofmp_rma_rma',
                'rma_id'
            ),
            'rc_rma_id',
            $installer->getTable('lofmp_rma_rma'),
            'rma_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

                $table = $installer->getConnection()->newTable(
                    $installer->getTable('lofmp_rma_rma_order')
                )->addColumn(
                    'rma_order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                    'Rma Order Id'
                )->addColumn(
                    're_rma_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => false],
                    'Re Rma Id'
                )->addColumn(
                    're_exchange_order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => false, 'nullable' => true],
                    'Re Exchange Order Id'
                )->addIndex(
                    $installer->getIdxName('lofmp_rma_rma_order', ['re_rma_id']),
                    ['re_rma_id']
                )->addForeignKey(
                    $installer->getFkName(
                        'lofmp_rma_rma_order',
                        're_rma_id',
                        'lofmp_rma_rma',
                        'rma_id'
                    ),
                    're_rma_id',
                    $installer->getTable('lofmp_rma_rma'),
                    'rma_id',
                    Table::ACTION_CASCADE
                );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_rma_store')
        )->addColumn(
            'rma_store_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Rma Store Id'
        )->addColumn(
            'rs_rma_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Rs Rma Id'
        )->addColumn(
            'rs_store_id',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Rs Store Id'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma_store', ['rs_rma_id']),
            ['rs_rma_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rma_store', ['rs_store_id']),
            ['rs_store_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma_store',
                'rs_rma_id',
                'lofmp_rma_rma',
                'rma_id'
            ),
            'rs_rma_id',
            $installer->getTable('lofmp_rma_rma'),
            'rma_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rma_store',
                'rs_store_id',
                'store',
                'store_id'
            ),
            'rs_store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_rule')
        )->addColumn(
            'rule_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'event',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Event'
        )->addColumn(
            'email_subject',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Email Subject'
        )->addColumn(
            'email_body',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Email Body'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Is Active'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Conditions Serialized'
        )->addColumn(
            'is_send_owner',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send Owner'
        )->addColumn(
            'is_send_department',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send Department'
        )->addColumn(
            'is_send_user',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send User'
        )->addColumn(
            'other_email',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Other Email'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'is_stop_processing',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Stop Processing'
        )->addColumn(
            'status_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Status Id'
        )->addColumn(
            'user_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true],
            'User Id'
        )->addColumn(
            'is_send_attachment',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Send Attachment'
        )->addColumn(
            'is_resolved',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Resolved'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rule', ['status_id']),
            ['status_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_rule', ['user_id']),
            ['user_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rule',
                'status_id',
                'lofmp_rma_status',
                'status_id'
            ),
            'status_id',
            $installer->getTable('lofmp_rma_status'),
            'status_id',
            Table::ACTION_SET_NULL
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_rule',
                'user_id',
                'admin_user',
                'user_id'
            ),
            'user_id',
            $installer->getTable('admin_user'),
            'user_id',
            Table::ACTION_SET_NULL
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_status')
        )->addColumn(
            'status_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Status Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'sort_order',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Sort Order'
        )->addColumn(
            'is_show_shipping',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Rma Resolved'
        )->addColumn(
            'customer_message',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Customer Message'
        )->addColumn(
            'admin_message',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Admin Message'
        )->addColumn(
            'history_message',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'History Message'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        )->addColumn(
            'code',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Code'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_template')
        )->addColumn(
            'template_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Id'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => false],
            'Name'
        )->addColumn(
            'template',
            Table::TYPE_TEXT,
            '64K',
            ['unsigned' => false, 'nullable' => true],
            'Template'
        )->addColumn(
            'allow_seller',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Allow seller use this template'
        )->addColumn(
            'is_active',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'default' => 0],
            'Is Active'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_rma_template_store')
        )->addColumn(
            'template_store_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Template Store Id'
        )->addColumn(
            'ts_template_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Ts Template Id'
        )->addColumn(
            'ts_store_id',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'nullable' => false],
            'Ts Store Id'
        )->addIndex(
            $installer->getIdxName('lofmp_rma_template_store', ['ts_template_id']),
            ['ts_template_id']
        )->addIndex(
            $installer->getIdxName('lofmp_rma_template_store', ['ts_store_id']),
            ['ts_store_id']
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_template_store',
                'ts_template_id',
                'lofmp_rma_template',
                'template_id'
            ),
            'ts_template_id',
            $installer->getTable('lofmp_rma_template'),
            'template_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'lofmp_rma_template_store',
                'ts_store_id',
                'store',
                'store_id'
            ),
            'ts_store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

         $installer->getConnection()->dropTable($installer->getTable('lofmp_rma_order_status_history'));
            $table = $installer->getConnection()->newTable(
                $installer->getTable('lofmp_rma_order_status_history')
            )->addColumn(
                'history_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
                'Order Id'
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false, 'primary' => false],
                'Order Id'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                32,
                ['unsigned' => false, 'nullable' => false],
                'Status'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Created At'
            );
            $installer->getConnection()->createTable($table);
    }
}
