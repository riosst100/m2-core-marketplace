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
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Setup;

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
        $setup->getConnection()->dropTable($setup->getTable('lofmp_storelocator_storelocator'));
        $setup->getConnection()->dropTable($setup->getTable('lofmp_storelocator_storelocator_store'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_storelocator_storelocator')
        )
        ->addColumn(
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'StoreLocator ID'
        )
        ->addColumn(
            'seller_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Seller ID'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )
        ->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Description'
        )
        ->addColumn(
            'link',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Link'
        )
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'Position'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Number Column on Tablets'
        )

        // image
        ->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Image'
        )

        // gmap
        ->addColumn(
            'address',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Address'
        )
        ->addColumn(
            'address2',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Address 2'
        )
        ->addColumn(
            'telephone',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => true],
            'Telephone'
        )
        ->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Email'
        )
        ->addColumn(
            'website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Website'
        )
        ->addColumn(
            'city',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'City'
        )
        ->addColumn(
            'zipcode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Zipcode'
        )

        ->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Country'
        )
        ->addColumn(
            'hours',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours'
        )
        ->addColumn(
            'hours1',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours1'
        )
        ->addColumn(
            'hours2',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours2'
        )
        ->addColumn(
            'hours3',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours3'
        )
        ->addColumn(
            'hours4',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours4'
        )
        ->addColumn(
            'hours5',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours5'
        )
        ->addColumn(
            'hours6',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Hours6'
        )

        ->addColumn(
            'zoomlevel',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Zoomlevel'
        )
        ->addColumn(
            'lat',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Latitude'
        )
        ->addColumn(
            'lng',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => false],
            'Longitude'
        )


        // social
        ->addColumn(
            'linkedin',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Linkedin'
        )
        ->addColumn(
            'facebook',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Facebook'
        )
        ->addColumn(
            'twitter',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Twitter'
        )
        ->addColumn(
            'youtube',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Youtube'
        )
        ->addColumn(
            'vimeo',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Vimeo'
        )
        ->addColumn(
            'googleplus',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Google Plus'
        )
        ->addColumn(
            'color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Color'
        )
        ->addColumn(
            'fontClass',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Font Class'
        )

        ->addColumn(
            'pagetitle',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Page Title'
        )
        // SEO
        ->addColumn(
            'keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Meta Keywords'
        )
        ->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            256,
            ['nullable' => false],
            'Meta Description'
        )
        
        ->addIndex(
            $setup->getIdxName(
                $installer->getTable('lofmp_storelocator_storelocator'),
                ['name', 'email'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name', 'email'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )
        ->setComment('StoreLocator - StoreLocator Table')
        ->setOption('type', 'InnoDB')
        ->setOption('charset', 'utf8');

        
        $installer->getConnection()->createTable($table);

        
        /**
         * Create table 'lofmp_storelocator_storelocator_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_storelocator_storelocator_store')
        )->addColumn(
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Storelocator ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('lofmp_storelocator_storelocator_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('lofmp_storelocator_storelocator_store', 'storelocator_id', 'lofmp_storelocator_storelocator', 'storelocator_id'),
            'storelocator_id',
            $installer->getTable('lofmp_storelocator_storelocator'),
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_storelocator_storelocator_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'CMS Page To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * create table tag
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_storelocator_tag')
        )->addColumn(
            'tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Tag ID'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Description'
        )->addColumn(
                'seller_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Seller ID'
            )
        ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Creation Time'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Update Time'
            )
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'position'
        );
        $installer->getConnection()->createTable($table);

        /**
         * create table cateogires
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_storelocator_category')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'Description'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            256,
            ['nullable' => true],
            'image'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true],
            'position'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lofmp_storelocator_storelocator_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_storelocator_storelocator_tag')
        )->addColumn(
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Storelocator ID'
        )->addColumn(
            'tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Tag ID'
        )->addIndex(
            $installer->getIdxName('lofmp_storelocator_storelocator_tag', ['tag_id']),
            ['tag_id']
        )->addForeignKey(
            $installer->getFkName('lofmp_storelocator_storelocator_tag', 'storelocator_id', 'lofmp_storelocator_storelocator', 'storelocator_id'),
            'storelocator_id',
            $installer->getTable('lofmp_storelocator_storelocator'),
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_storelocator_storelocator_tag', 'tag_id', 'lofmp_storelocator_tag', 'tag_id'),
            'tag_id',
            $installer->getTable('lofmp_storelocator_tag'),
            'tag_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'CMS Page To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

         /**
         * Create table 'lofmp_storelocator_storelocator_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lofmp_storelocator_storelocator_category')
        )->addColumn(
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Storelocator ID'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addIndex(
            $installer->getIdxName('lofmp_storelocator_storelocator_category', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $installer->getFkName('lofmp_storelocator_storelocator_category', 'storelocator_id', 'lofmp_storelocator_storelocator', 'storelocator_id'),
            'storelocator_id',
            $installer->getTable('lofmp_storelocator_storelocator'),
            'storelocator_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('lofmp_storelocator_storelocator_category', 'category_id', 'store', 'category_id'),
            'category_id',
            $installer->getTable('lofmp_storelocator_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'CMS Page To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        //end schema
    }
}