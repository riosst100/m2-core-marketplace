<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Model\Rewrite\Quote;

class Item extends \Magento\Quote\Model\Quote\Item
{
    /**
     * Check product representation in item.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function representProduct($product)
    {
        $itemProduct = $this->getProduct();
        if (!$product || $itemProduct->getId() != $product->getId()) {
            return false;
        }
        /*
         * Check maybe product is planned to be a child of some quote item - in this case we limit search
         * only within same parent item
         */
        $stickWithinParent = $product->getStickWithinParent();
        if ($stickWithinParent) {
            if ($this->getParentItem() !== $stickWithinParent) {
                return false;
            }
        }
        // Check options
        $itemOptions = $this->getOptionsByCode();
        $productOptions = $product->getCustomOptions();
        if (!$this->compareOptions($itemOptions, $productOptions)) {
            return false;
        }
        if (!$this->compareOptions($productOptions, $itemOptions)) {
            return false;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->get('Lofmp\PriceComparison\Helper\Data');
        if ($helper->isNewProduct() || $helper->getFullActionName() == "lofmppricecomparison_order_reorder") {
            return false;
        }

        return true;
    }
}
