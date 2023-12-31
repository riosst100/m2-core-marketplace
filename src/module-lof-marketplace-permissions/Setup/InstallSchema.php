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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $table_lof_marketplace_permissions_advanced_customer_entity = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_advanced_customer_entity'))
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => false,
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Customer ID'
            )->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => false,
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Seller ID'
            )->addColumn(
                'job_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true
                ],
                'Job Title'
            )->addColumn(
                'telephone',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true
                ],
                'Phone Number'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'identity' => false,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 1,
                ],
                'Status'
            )->addIndex(
                $setup->getIdxName(
                    $setup->getTable('lof_marketplace_advanced_customer_entity'),
                    ['customer_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['customer_id'],
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName('lof_marketplace_advanced_customer_entity', ['status']),
                ['status']
            )->addForeignKey(
                $setup->getFkName(
                    'lof_marketplace_advanced_customer_entity',
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $table_lof_marketplace_permissions_team = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_team'))
            ->addColumn(
                'team_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'primary' => true,
                    'nullable' => false,
                    'unsigned' => false,
                ],
                'Team ID'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                40,
                [
                    'nullable' => true,
                ],
                'Name'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                ],
                'Description'
            );

        $table_lof_marketplace_permissions_structure = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_structure'))
            ->addColumn(
                'structure_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'primary' => true,
                    'nullable' => false,
                    'unsigned' => false,
                ],
                'Structure ID'
            )->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Parent Structure ID'
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Entity ID'
            )->addColumn(
                'entity_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Entity type'
            )->addColumn(
                'path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Entity type'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => false,
                    'nullable' => false,
                    'identity' => false
                ],
                'Position'
            )->addColumn(
                'level',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => false,
                    'nullable' => false,
                    'identity' => false,
                    'default' => 0
                ],
                'Tree Level'
            )->addIndex(
                $setup->getIdxName('lof_marketplace_structure', ['parent_id', 'entity_id', 'entity_type']),
                ['parent_id', 'entity_id', 'entity_type']
            );

        $table_lof_marketplace_permissions_roles = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_roles'))
            ->addColumn(
                'role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'primary' => true,
                    'nullable' => false
                ],
                'Primary Role ID'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Sorting order'
            )->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Seller ID'
            )->addColumn(
                'role_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                40,
                [
                    'nullable' => true,
                ],
                'Seller role name'
            )->addIndex(
                $setup->getIdxName('lof_marketplace_roles', ['seller_id']),
                ['seller_id']
            )->addForeignKey(
                $setup->getFkName(
                    'lof_marketplace_roles',
                    'seller_id',
                    'lof_marketplace_seller',
                    'seller_id'
                ),
                'seller_id',
                $setup->getTable('lof_marketplace_seller'),
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $table_lof_marketplace_permissions_user_roles = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_user_roles'))
            ->addColumn(
                'user_role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'primary' => true,
                    'nullable' => false,
                    'unsigned' => false,
                ],
                'Primary User Role ID'
            )->addColumn(
                'role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Role ID'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'User ID'
            )->addIndex(
                $setup->getIdxName('lof_marketplace_user_roles', ['role_id', 'user_id']),
                ['role_id', 'user_id']
            )->addForeignKey(
                $setup->getFkName(
                    'lof_marketplace_user_roles',
                    'role_id',
                    'lof_marketplace_roles',
                    'role_id'
                ),
                'role_id',
                $setup->getTable('lof_marketplace_roles'),
                'role_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'lof_marketplace_user_roles',
                    'user_id',
                    'customer_entity',
                    'entity_id'
                ),
                'user_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $table_lof_marketplace_permissions_permissions = $setup->getConnection()
            ->newTable($setup->getTable('lof_marketplace_permissions'))
            ->addColumn(
                'permission_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => false,
                    'nullable' => false,
                    'primary' => true
                ],
                'Primary Permission ID'
            )->addColumn(
                'role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => false
                ],
                'Role ID'
            )->addColumn(
                'resource_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                80,
                [
                    'nullable' => true
                ],
                'Resource ID'
            )->addColumn(
                'permission',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                80,
                [
                    'nullable' => true
                ],
                'Permission'
            )->addIndex(
                $setup->getIdxName('lof_marketplace_permissions', ['role_id']),
                ['role_id']
            )->addForeignKey(
                $setup->getFkName(
                    'lof_marketplace_permissions',
                    'role_id',
                    'lof_marketplace_roles',
                    'role_id'
                ),
                'role_id',
                $setup->getTable('lof_marketplace_roles'),
                'role_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

        $setup->getConnection()->createTable($table_lof_marketplace_permissions_roles);
        $setup->getConnection()->createTable($table_lof_marketplace_permissions_permissions);
        $setup->getConnection()->createTable($table_lof_marketplace_permissions_user_roles);
        $setup->getConnection()->createTable($table_lof_marketplace_permissions_structure);
        $setup->getConnection()->createTable($table_lof_marketplace_permissions_advanced_customer_entity);
        $setup->getConnection()->createTable($table_lof_marketplace_permissions_team);
    }
}
