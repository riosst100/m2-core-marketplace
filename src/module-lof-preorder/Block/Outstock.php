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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Block;

class Outstock extends \Magento\Swatches\Block\Product\Renderer\Configurable
{

    public function getAllowProducts()
    {

        if (!$this->hasAllowProducts()) {
             $skipSaleableCheck = 1;
             $products = $skipSaleableCheck ?
             $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null) :
                $this->getProduct()->getTypeInstance()->getSalableUsedProducts($this->getProduct(), null);
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
}
