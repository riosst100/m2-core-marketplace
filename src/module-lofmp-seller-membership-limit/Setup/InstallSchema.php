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
 * @package    SellerMembershipLimit
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembershipLimit\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /* table lof_marketplace_group */
        $groupTable = $installer->getTable('lof_marketplace_group');

        $installer->getConnection()->addColumn(
            $groupTable,
            'limit_product_duration',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Limit Products per Duration'
            ]
        );

        $installer->getConnection()->addColumn(
            $groupTable,
            'limit_auction_duration',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Limit Auctions per Duration'
            ]
        );

        $installer->getConnection()->addColumn(
            $groupTable,
            'trial_days',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Trial Days'
            ]
        );

        $membershipTable = $installer->getTable('lofmp_marketplace_membership');

        $installer->getConnection()->addColumn(
            $membershipTable,
            'limit_product_duration',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Limit Products per Duration'
            ]
        );

        $installer->getConnection()->addColumn(
            $membershipTable,
            'limit_auction_duration',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'length' => 10,
                'nullable' => true,
                'comment' => 'Limit Auctions per Duration'
            ]
        );

        $installer->endSetup();
    }
}
