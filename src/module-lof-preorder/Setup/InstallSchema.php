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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Preorder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface as SchemaSetup;
use Magento\Framework\Setup\ModuleContextInterface as ModuleContext;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetup $setup, ModuleContext $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->moveDirToMediaDir();
        /*
         * Create table 'lof_preorder_items'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_preorder_items'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Id'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Order Id'
            )
            ->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Item Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Product Id'
            )
            ->addColumn(
                'parent_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Parent Product Id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Customer Id'
            )
            ->addColumn(
                'customer_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Email'
            )
            ->addColumn(
                'preorder_percent',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Preorder Percent'
            )
            ->addColumn(
                'paid_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Paid Amount'
            )
            ->addColumn(
                'remaining_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Remaining Amount'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Quantity'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Preorder Type'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'time',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Time'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ],
                'Created At'
            )
            ->setComment('Preorder Items Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_preorder_quote_item'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Id'
            )
            ->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Item Id'
            )
            ->addColumn(
                'preorder_percent',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Preorder Percent'
            )
            ->setComment('Preorder Quote Item Table');
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable($installer->getTable('lof_preorder_complete_item'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Id'
            )
            ->addColumn(
                'order_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Item Id'
            )
            ->addColumn(
                'quote_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Quote Id'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Order Id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Customer Id'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Product Id'
            )
            ->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                11,
                ['nullable' => false],
                'Quantity'
            )
            ->setComment('Preorder Complete Item Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

    private function moveDirToMediaDir()
    {
        try {
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            $reader = $objManager->get('Magento\Framework\Module\Dir\Reader');
            $filesystem = $objManager->get('Magento\Framework\Filesystem');
            $type = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;
            $path = $filesystem->getDirectoryRead($type)
                                ->getAbsolutePath().'preorder/images/';
            $modulePath = $reader->getModuleDir('', 'Lof_Preorder');
            $mediaFile = $modulePath.'/view/frontend/web/images/preorder.png';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            $filePath = $path.'preorder.png';
            if (!file_exists($filePath)) {
                if (file_exists($mediaFile)) {
                    copy($mediaFile, $filePath);
                }
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }
    }
}
