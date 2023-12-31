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

namespace Lof\MarketPlace\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class DropSellerGroupForeignKey implements SchemaPatchInterface
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

        $this->deleteGroupIdForeignKey($this->moduleDataSetup);

        $this->moduleDataSetup->endSetup();
    }

    /**
     * @param $installer
     */
    protected function deleteGroupIdForeignKey($installer)
    {
        $installer->getConnection()->dropForeignKey(
            $installer->getTable('lof_marketplace_seller'),
            $this->schemaSetup->getFkName('lof_marketplace_seller', 'group_id', 'lof_marketplace_seller', 'group_id')
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
