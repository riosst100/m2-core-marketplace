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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

class InstallSchema implements InstallSchemaInterface
{

    const LOF_SELLER_ATTACHMENT_TABLE = 'lof_marketplace_seller_attachment';

    const LOF_SELLER_TABLE = 'lof_marketplace_seller';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists(self::LOF_SELLER_ATTACHMENT_TABLE)) {
            $sellerAttachmentTable = $installer->getConnection()->newTable(
                $installer->getTable(self::LOF_SELLER_ATTACHMENT_TABLE)
            )
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
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
                    'seller_id',
                    Table::TYPE_INTEGER,
                    10,
                    [
                        'nullable' => false,
                        'unsigned' => true,
                    ],
                    'Seller ID'
                )
                ->addColumn(
                    'file_name',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => true,
                    ],
                    'File Name'
                )
                ->addColumn(
                    'file_path',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'File Path'
                )
                ->addColumn(
                    'file_type',
                    Table::TYPE_TEXT,
                    32,
                    [
                        'nullable' => true,
                    ],
                    'File Type'
                )
                ->addColumn(
                    'identify_type',
                    Table::TYPE_TEXT,
                    32,
                    [
                        'nullable' => true,
                    ],
                    'Identify Type'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                    'Created At'
                )->addForeignKey(
                    $installer->getFkName(
                        self::LOF_SELLER_ATTACHMENT_TABLE,
                        'seller_id',
                        self::LOF_SELLER_TABLE,
                        'seller_id'
                    ),
                    'seller_id',
                    $installer->getTable(self::LOF_SELLER_TABLE),
                    'seller_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Lof MarketPlace Seller Attachment');

            $installer->getConnection()->createTable($sellerAttachmentTable);
        }
        $installer->endSetup();
    }
}
