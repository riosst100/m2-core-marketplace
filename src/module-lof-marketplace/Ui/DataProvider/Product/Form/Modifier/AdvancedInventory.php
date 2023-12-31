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

namespace Lof\MarketPlace\Ui\DataProvider\Product\Form\Modifier;

/**
 * Data provider for advanced inventory form
 */
class AdvancedInventory extends \Magento\CatalogInventory\Ui\DataProvider\Product\Form\Modifier\AdvancedInventory
{
    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Lof\MarketPlace\Helper\Data::class);
        $enabled = $helper->getConfig('seller_settings/allow_product_advanced_inventory');
        $meta = parent::modifyMeta($meta);
        if ($enabled) {
            return $meta;
        }

        if (isset($meta['product-details']['children']['quantity_and_stock_status_qty']['children']['advanced_inventory_button'])) {
            unset($meta['product-details']['children']['quantity_and_stock_status_qty']['children']['advanced_inventory_button']);
        }
        return $meta;
    }
}
