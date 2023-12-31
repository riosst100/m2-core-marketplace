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
 * Class for Product Modifier Advanced Pricing
 *
 * @api
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdvancedPricing extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AdvancedPricing
{
    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get(\Lof\MarketPlace\Helper\Data::class);
        $enabled = $helper->getConfig('seller_settings/allow_product_advanced_pricing');
        if ($enabled) {
            return parent::modifyMeta($meta);
        }

        if (isset($meta['advanced-pricing'])) {
            unset($meta['advanced-pricing']);
        }
        $this->meta = $meta;

        return $this->meta;
    }
}
