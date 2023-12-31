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

namespace Lof\MarketPlace\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product;

class InstallData implements InstallDataInterface
{
    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     *
     * @param GroupFactory $groupFactory
     */
    public function __construct(GroupFactory $groupFactory, CategorySetupFactory $categorySetupFactory)
    {
        $this->groupFactory = $groupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        /** @var CustomerSetup $customerSetup */
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $setup->startSetup();

        $categorySetup->addAttribute(
            Product::ENTITY,
            'seller_id',
            [
                'group' => 'Product Details',
                'label' => 'Seller Id',
                'type' => 'static',
                'input' => 'text',
                'position' => 145,
                'visible' => true,
                'default' => '',
                'required' => false,
                'user_defined' => false,
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true
            ]
        );

        $categorySetup->addAttribute(
            Product::ENTITY,
            'approval',
            [
                'group' => 'Product Details',
                'label' => 'Approval',
                'type' => 'static',
                'input' => 'select',
                'position' => 160,
                'visible' => true,
                'default' => '',
                'required' => false,
                'user_defined' => false,
                // phpcs:disable Magento2.PHP.LiteralNamespaces.LiteralClassUsage
                'source' => 'Lof\MarketPlace\Model\Source\Approval',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'used_in_product_listing' => true
            ]
        );

        $this->initSellerGroup($setup, $context);

        $setup->endSetup();
    }
    /**
     * {@inheritdoc}
     */
    public function initSellerGroup(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * install group affiliate
         */
        $data = [
            [
                'name' => 'Default',
                'url_key' => 'default',
                'status' => 1,
                'position' => 0,
                'shown_in_sidebar' => 1
            ]
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('lof_marketplace_group'), $row);
        }
    }
}
