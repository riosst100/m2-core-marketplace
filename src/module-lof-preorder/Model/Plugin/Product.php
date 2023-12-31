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
 namespace Lof\PreOrder\Model\Plugin;

use Magento\Catalog\Model\Product as CatalogProduct;

class Product
{
    /**
     * @var \Lof\Preorder\Helper\Data
     */
    private $_preorderHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Lof\Preorder\Helper\Data $preorderHelper
     */
    public function __construct(
        \Lof\PreOrder\Helper\Data $preorderHelper
    ) {
        $this->_preorderHelper = $preorderHelper;
    }

    public function afterIsSalable(CatalogProduct $subject, $result)
    {

        $productId = $subject->getId();
        $helper = $this->_preorderHelper;

        if ($helper->isPreorder($productId) && !$helper->isChildProduct()) {
            return true;
        } elseif ($helper->isConfigPreorder($productId)) {
            return true;
        }

        return $result;
    }
}
