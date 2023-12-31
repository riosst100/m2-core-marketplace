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
 * @package    Lofmp_SplitOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrder\Api;

/**
 * Interface ExtensionAttributesInterface
 * @api
 */
interface ExtensionAttributesInterface
{
    /**
     * @var string
     */
    const QUANTITY_AND_STOCK_STATUS = 'quantity_and_stock_status';

    /**
     * Method to cover extra attributes which need a different load model.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeCode
     * @return bool|string
     */
    public function loadValue($product, $attributeCode);

    /**
     * Handle Stock attribute data.
     *
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterface $attributes
     * @return string|float
     */
    public function quantityAndStockStatus($attributes);
}
