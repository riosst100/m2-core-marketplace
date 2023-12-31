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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
	/**
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		EavSetupFactory $eavSetupFactory
	) {
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();
		if ($context->getVersion()
            && version_compare($context->getVersion(), '1.0.4') < 0
        ) {
	 		
	 		$product_disable_quickrfq = array(
					'group'                         => 'General',
					'type'                          => 'int',
					'input'                         => 'boolean',
					'default'                       => 0,
					'label'                         => 'Disable Quick Quote',
					'backend'                       => '',
					'frontend'                      => '',
					'source'                        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
					'visible'                       => 1,
					'required'                      => 0,
					'user_defined'                  => 1,
					'used_for_price_rules'          => 1,
					'position'                      => 2,
					'unique'                        => 0,
					'sort_order'                    => 102,
					'is_global'                     => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
					'is_required'                   => 0,
					'is_configurable'               => 1,
					'is_searchable'                 => 0,
					'is_visible_in_advanced_search' => 0,
					'is_comparable'                 => 0,
					'is_filterable'                 => 0,
					'is_filterable_in_search'       => 1,
					'is_used_for_promo_rules'       => 1,
					'is_html_allowed_on_front'      => 0,
					'is_visible_on_front'           => 1,
					'used_in_product_listing'       => 1,
					'used_for_sort_by'              => 0,
	 			);

            $product_disable_upload_quickrfq = array(
                'group'                         => 'General',
                'type'                          => 'int',
                'input'                         => 'boolean',
                'default'                       => 0,
                'label'                         => 'Disable Upload File for QuickRFQ',
                'backend'                       => '',
                'frontend'                      => '',
                'source'                        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'visible'                       => 1,
                'required'                      => 0,
                'user_defined'                  => 1,
                'used_for_price_rules'          => 1,
                'position'                      => 2,
                'unique'                        => 0,
                'sort_order'                    => 103,
                'is_global'                     => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'is_required'                   => 0,
                'is_configurable'               => 1,
                'is_searchable'                 => 0,
                'is_visible_in_advanced_search' => 0,
                'is_comparable'                 => 0,
                'is_filterable'                 => 0,
                'is_filterable_in_search'       => 1,
                'is_used_for_promo_rules'       => 1,
                'is_html_allowed_on_front'      => 0,
                'is_visible_on_front'           => 1,
                'used_in_product_listing'       => 1,
                'used_for_sort_by'              => 0,
            );

            $eavSetup1 = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup2 = $this->eavSetupFactory->create(['setup' => $setup]);
	 		$eavSetup1->addAttribute(
	 			\Magento\Catalog\Model\Product::ENTITY,
	 			'quickrfq_disable',
	 			$product_disable_quickrfq);
            $eavSetup2->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'quickrfq_disable_upload_file',
                $product_disable_quickrfq);
	 	}
 		$setup->endSetup();
	}
}
