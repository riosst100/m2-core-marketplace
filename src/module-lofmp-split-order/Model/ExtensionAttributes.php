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

namespace Lofmp\SplitOrder\Model;

use Lofmp\SplitOrder\Helper\Data as HelperData;
use Lofmp\SplitOrder\Api\ExtensionAttributesInterface;

class ExtensionAttributes implements ExtensionAttributesInterface
{
    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @param HelperData $helperData
     */
    public function __construct(
        HelperData $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * @inheritdoc
     */
    public function loadValue($product, $attributeCode)
    {
        /** @var \Magento\Catalog\Api\Data\ProductExtensionInterface $attributes */
        $attributes = $product->getExtensionAttributes();
        if ($attributeCode === self::QUANTITY_AND_STOCK_STATUS) {
            return (string)$this->quantityAndStockStatus($attributes);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function quantityAndStockStatus($attributes)
    {
        if ($this->helperData->getQtyType() === 'qty') {
            return $attributes->getStockItem()->getQty();
        }
        if ($this->helperData->getBackorder() && $attributes->getStockItem()->getQty() < 1) {
            return 'out';
        }
        return ($attributes->getStockItem()->getIsInStock()) ? 'in' : 'out';
    }
}
