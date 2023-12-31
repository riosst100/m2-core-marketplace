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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Model\ShippingCommission as ShippingCommissionRule;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculateShippingCommission
{
    /**
     * @param $commission
     * @param $price
     * @return float|int|mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function calculate($commission, $amount)
    {
        if (is_array($commission)) {
            $_commission = $commission['commission_amount'];
            switch ($commission['commission_by']) {
                case ShippingCommissionRule::COMMISSION_BY_FIXED_AMOUNT:
                    $_commission = $commission['commission_amount'];
                    break;
                case ShippingCommissionRule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
                    $_commission = ((float)$commission['commission_amount'] * $amount) / 100;
                    break;
            }
            return $_commission;
        } else {
            if ($commission != 0) {
                $_commission = ((float)$commission * $amount) / 100;
            } else {
                $_commission = 0;
            }

            return $_commission;
        }
    }
}
