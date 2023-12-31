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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Model\Carrier\Flatrate;

use Magento\Quote\Model\Quote\Address\RateRequest;

class ItemPriceCalculator
{
    /**
     * @param RateRequest $request
     * @param int $basePrice
     * @param int $freeBoxes
     * @return float
     */
    public function getShippingPricePerItem(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $basePrice,
        $freeBoxes
    ) {
        return $request->getPackageQty() * $basePrice - $freeBoxes * $basePrice;
    }

    /**
     * @param RateRequest $request
     * @param int $basePrice
     * @param int $freeBoxes
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getShippingPricePerOrder(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $basePrice,
        $freeBoxes
    ) {
        return $basePrice;
    }
}
