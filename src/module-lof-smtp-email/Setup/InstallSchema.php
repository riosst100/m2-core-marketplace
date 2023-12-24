<?php


namespace Lof\SmtpEmail\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'lof_smtpemail_emaillog'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_smtpemail_emaillog'))
            ->addColumn(
                'emaillog_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )
            ->addColumn(
                'body',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'recipient_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )
            ->addColumn(
                'status',
                 \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                120,
                ['nullable' => false]
            )->addIndex(
                $setup->getIdxName(
                    $installer->getTable('lof_smtpemail_emaillog'),
                    ['subject', 'body', 'recipient_email'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['subject', 'body', 'recipient_email'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )->setComment('Lof SMTP Log Table');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'lof_smtpemail_emaildebug'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_smtpemail_emaildebug'))
            ->addColumn(
                'emaildebug_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->setComment('Lof SMTP Log Table');
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lof_smtpemail_blacklist'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_smtpemail_blacklist'))
            ->addColumn(
                'blacklist_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false]
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false]
            )->setComment('Lof SMTP Blacklist Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
