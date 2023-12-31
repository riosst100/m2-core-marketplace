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

class DropSellerForeignKey implements SchemaPatchInterface
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
        if ($this->schemaSetup->tableExists("lofmp_faq_category")) {
            $this->moduleDataSetup->getConnection()->modifyColumn(
                $this->moduleDataSetup->getTable('lofmp_faq_category'),
                'seller_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => 0,
                    'comment' => 'Seller ID'
                ]
            );

            $this->deleteGroupIdForeignKey($this->moduleDataSetup);
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param $installer
     */
    protected function deleteGroupIdForeignKey($installer)
    {
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('lofmp_faq_category'),
            $this->schemaSetup->getFkName('lofmp_faq_category', 'seller_id', 'lof_marketplace_seller','seller_id')
        );
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
