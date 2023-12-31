<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

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
         * Create table 'lofmp_faq_category'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_faq_category')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'parent_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true],
            'Parent ID'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Category Title'
        )->addColumn(
            'title_size',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 12],
            'Title Size'
        )->addColumn(
            'title_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#ffffff'],
            'Title Color'
        )->addColumn(
            'title_background',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#ffffff'],
            'Title Background'
        )->addColumn(
            'border_width',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Border Width'
        )->addColumn(
            'border_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#000000'],
            'Border Color'
        )->addColumn(
            'border_radius',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '0'],
            'Border Radius'
        )->addColumn(
            'body_background',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#ffffff'],
            'Body Background'
        )->addColumn(
            'margin_bottom',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Margin Bottom'
        )->addColumn(
            'margin_left',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Margin Left'
        )->addColumn(
            'icon',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Icon'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Description'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 1],
            'Position'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Category Status'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Category Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Category Modification Time'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('lofmp_faq_category'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_category', 'seller_id', 'lof_marketplace_seller','seller_id'),
            'seller_id',
            $installer->getTable('lof_marketplace_seller'),
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_category', 'store_id', 'store','store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'LOF MpFaq Category Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'lofmp_faq_question'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_faq_question')
        )->addColumn(
            'question_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Question ID'
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Seller ID'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Customer Name'
        )->addColumn(
            'customer_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Customer Email'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Category ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Question Title'
        )->addColumn(
            'title_size',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 12],
            'Title Size'
        )->addColumn(
            'title_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#ffffff'],
            'Title Color'
        )->addColumn(
            'title_background',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#000000'],
            'Title Background'
        )->addColumn(
            'border_width',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Border Width'
        )->addColumn(
            'border_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#000000'],
            'Border Color'
        )->addColumn(
            'border_radius',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 0],
            'Border Radius'
        )->addColumn(
            'body_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#ffffff'],
            'Body Color'
        )->addColumn(
            'body_background',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '#000000'],
            'Body Background'
        )->addColumn(
            'margin_bottom',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Question Margin Bottom'
        )->addColumn(
            'margin_left',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Question Margin Left'
        )->addColumn(
            'icon',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Question Icon'
        )->addColumn(
            'answer',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Question Answer'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Question Status'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'default' => 1],
            'Question Position'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Question Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Question Modification Time'
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_question', 'seller_id', 'lof_marketplace_seller','seller_id'),
            'seller_id',
            $installer->getTable('lof_marketplace_seller'),
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_question', 'category_id', 'lofmp_faq_category','category_id'),
            'category_id',
            $installer->getTable('lofmp_faq_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_question', 'store_id', 'store','store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('lofmp_faq_question'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'LOF MpFaq Question Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'lofmp_faq_question_product'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_faq_question_product')
        )->addColumn(
            'question_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'primary' => true],
            'Question ID'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product ID'
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_question_product', 'question_id', 'lofmp_faq_question','question_id'),
            'question_id',
            $installer->getTable('lofmp_faq_question'),
            'question_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_question_product', 'product_id', 'lof_marketplace_product','product_id'),
            'product_id',
            $installer->getTable('lof_marketplace_product'),
            'product_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Lof MpFaq Question To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /** table lofmp_faq_enable_product */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_faq_enable_product')
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product ID'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Faq Product Status'
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_enable_product', 'product_id', 'lof_marketplace_product','product_id'),
            'product_id',
            $installer->getTable('lof_marketplace_product'),
            'product_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'FAQ Enable Product Table'
        );
        $installer->getConnection()->createTable($table);

        /** table lofmp_faq_enable_seller */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_faq_enable_seller')
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Seller ID'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Faq Seller Status'
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_enable_seller', 'seller_id', 'lof_marketplace_seller','seller_id'),
            'seller_id',
            $installer->getTable('lof_marketplace_seller'),
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'FAQ Enable Seller Table'
        );
        $installer->getConnection()->createTable($table);

        /** table lofmp_faq_setting */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_faq_setting')
        )->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Seller ID'
        )->addColumn(
            'enable',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => true],
            'Enable FAQ'
        )->addColumn(
            'layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '1-column'],
            'Product Page FAQ Layout'
        )->addColumn(
            'grid_column',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 12],
            'Grid Column'
        )->addColumn(
            'answer_slide',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 'fast'],
            'Answer Slide Speed'
        )->addColumn(
            'answer_list_slide',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => 'fast'],
            'Answer List Slide Speed'
        )->addColumn(
            'show_author',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => true],
            'Show Author'
        )->addColumn(
            'show_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => true],
            'Show Date'
        )->addColumn(
            'recent_tab',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => true],
            'Show Recent Tab'
        )->addColumn(
            'popup_form',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => true],
            'Enable Popup Form'
        )->addColumn(
            'enable_recaptcha',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => true],
            'Enable reCatptcha'
        )->addForeignKey(
            $installer->getFkName('lofmp_faq_setting', 'seller_id', 'lof_marketplace_seller','seller_id'),
            'seller_id',
            $installer->getTable('lof_marketplace_seller'),
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'FAQ Settings Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
