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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Lofmp\SellerMembership\Model\Product\Type\SellerMembership;
use \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class InstallData implements InstallDataInterface
{
    /**
     * Customer collection factory
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $_customerCollectionFactory;

    /**
     * @var \Magento\Catalog\Setup\CategorySetupFactory
     */
    private $_categorySetupFactory;

    /**
     * Init
     *
     * @param CollectionFactory $customerCollectionFactory
     * @param \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    ) {
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_categorySetupFactory = $categorySetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $categorySetup = $this->_categorySetupFactory->create(
            ['setup' => $setup]
        );
        $setup->startSetup();

        $categorySetup->addAttribute(
            Product::ENTITY,
            'duration',
            [
                'group' => 'Product Details',
                'type' => 'text',
                'input' => 'text',
                'position' => 4,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => '',
                'backend' => 'Lofmp\SellerMembership\Model\Product\Attribute\Backend\SellerMembershipDropdown',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=> 'seller_membership',
            ]
        );
        $categorySetup->addAttribute(
            Product::ENTITY,
            'seller_group',
            [
                'group' => 'Product Details',
                'label' => 'Seller Group',
                'type' => 'varchar',
                'input' => 'select',
                'source' => 'Lofmp\SellerMembership\Model\Config\Source\Group',
                'position' => 5,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=> 'seller_membership',
            ]
        );
        $categorySetup->addAttribute(
            Product::ENTITY,
            'featured_package',
            [
                'group' => 'Product Details',
                'label' => 'Featured Package',
                'type' => 'varchar',
                'input' => 'boolean',
                'position' => 6,
                'visible' => true,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'apply_to'=> 'seller_membership',
            ]
        );
         $categorySetup->addAttribute(
             Product::ENTITY,
             'membership_order',
             [
                'group' => 'Product Details',
                'label' => 'Sort Order',
                'type' => 'static',
                'input' => 'text',
                'position' => 7,
                'visible' => true,
                'default' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'visible_on_front' => false,
                'unique' => false,
                'is_configurable' => false,
                'used_for_promo_rules' => true,
                'is_used_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true
             ]
         );

          /*make sure these attributes are applied for membership product type only*/
        $attributes = [
            'seller_group',
            'duration',
            'featured_package',
            'membership_order',
        ];
        foreach ($attributes as $attributeCode) {
            $attribute = $categorySetup->getAttribute(Product::ENTITY, $attributeCode);
            $categorySetup->updateAttribute(Product::ENTITY, $attributeCode, 'apply_to', SellerMembership::TYPE_CODE);
        }

        $fieldList = [
            'tax_class_id',
        ];

        // make these attributes applicable to vendor membership products
        foreach ($fieldList as $field) {
            $applyTo = explode(
                ',',
                $categorySetup->getAttribute(Product::ENTITY, $field, 'apply_to')
            );
            if (!in_array(SellerMembership::TYPE_CODE, $applyTo)) {
                $applyTo[] = SellerMembership::TYPE_CODE;
                $categorySetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $field,
                    'apply_to',
                    implode(',', $applyTo)
                );
            }
        }

        $setup->endSetup();
    }
}
