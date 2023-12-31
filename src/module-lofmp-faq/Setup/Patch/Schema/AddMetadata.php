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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Faq\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddMetadata implements SchemaPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var SchemaSetupInterface
     */
    protected $schemaSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SchemaSetupInterface $schemaSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @return AddParentColumns|void
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        if ($this->schemaSetup->tableExists("lofmp_faq_category") && $this->schemaSetup->tableExists("lofmp_faq_question") && $this->schemaSetup->tableExists("lofmp_faq_question_product")) {

            $metadataColumns = [
                'meta_keywords' => 'Page Meta Keywords',
                'meta_description' => 'Page Meta Description',
                'page_title' => 'Page Title'
            ];
            foreach ($metadataColumns as $key => $label) {
                $this->moduleDataSetup->getConnection()->addColumn(
                    $this->moduleDataSetup->getTable('lofmp_faq_category'),
                    $key,
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => $label
                    ]
                );
                $this->moduleDataSetup->getConnection()->addColumn(
                    $this->moduleDataSetup->getTable('lofmp_faq_question'),
                    $key,
                    [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'comment' => $label
                    ]
                );
            }

            $this->moduleDataSetup->getConnection()->modifyColumn(
                $this->moduleDataSetup->getTable('lofmp_faq_category'),
                'category_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'primary' => true,
                    'identity' => true,
                    'comment' => 'Category ID'
                ]
            );

            $this->moduleDataSetup->getConnection()->modifyColumn(
                $this->moduleDataSetup->getTable('lofmp_faq_question'),
                'category_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Category ID'
                ]
            );

            $this->moduleDataSetup->getConnection()->modifyColumn(
                $this->moduleDataSetup->getTable('lofmp_faq_question'),
                'question_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'primary' => true,
                    'identity' => true,
                    'comment' => 'Question ID'
                ]
            );

            $this->moduleDataSetup->getConnection()->modifyColumn(
                $this->moduleDataSetup->getTable('lofmp_faq_category'),
                'parent_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'unsigned' => true,
                    'comment' => 'Category Parent ID'
                ]
            );

            $this->moduleDataSetup->getConnection()->modifyColumn(
                $this->moduleDataSetup->getTable('lofmp_faq_question_product'),
                'question_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Question ID'
                ]
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
