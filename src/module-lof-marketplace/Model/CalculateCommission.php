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

use Lof\MarketPlace\Model\Commission as CommissionRule;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculateCommission
{
    /**
     * @var bool
     */
    protected $_calculate_fixed_qty = true;

    /**
     * @param mixed|object $commission
     * @param mixed|object $price
     * @return float|int|mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function calculate($commission, $price)
    {
        $allow_calculate_fixed_qty = $this->getAllowFixedQty();
        if (is_array($commission)) {
            $_commission = $commission['commission_amount'];
            $qty = (int)$price->getData('qty');
            $qty_ordered = (int)$price->getData('qty_ordered');
            $qty = $qty_ordered > 0 ? $qty_ordered : $qty;
            $qty = $qty > 0 ? $qty : 1;
            switch ($commission['commission_by']) {
                case CommissionRule::COMMISSION_BY_FIXED_AMOUNT:
                    if ($allow_calculate_fixed_qty) {
                        $_commission = $commission['commission_amount'] * $qty;
                    } else {
                        $_commission = $commission['commission_amount'];
                    }
                    break;
                case CommissionRule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
                    if (!$price->getData('base_row_total')) {
                        $baseRowTotal = ($price->getData('price_incl_tax') * $qty)
                            - $price->getData('base_tax_amount');
                        $price->setData('base_row_total', $baseRowTotal);
                    }
                    switch ($commission['commission_action']) {
                        case CommissionRule::COMMISSION_BASED_PRICE_INCL_TAX:
                            $amount = $price->getData('base_row_total') + $price->getData('base_tax_amount');
                            break;
                        case CommissionRule::COMMISSION_BASED_PRICE_EXCL_TAX:
                            $amount = $price->getData('base_row_total');
                            break;
                        case CommissionRule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_INCL_TAX:
                            $amount = $price->getData('base_row_total')
                                - $price->getData('base_discount_amount')
                                + $price->getData('base_tax_amount');
                            break;
                        case CommissionRule::COMMISSION_BASED_PRICE_AFTER_DISCOUNT_EXCL_TAX:
                            $amount = $price->getData('base_row_total') - $price->getData('base_discount_amount');
                            break;
                        default:
                            $amount = $price->getData('base_row_total') - $price->getData('base_discount_amount');
                    }
                    $_commission = ($commission['commission_amount'] * $amount) / 100;
                    break;
            }
            return $_commission;
        } else {
            if ($commission != 0) {
                $commissionPerProduct = ($price->getData('row_total') - $price->getData('discount_amount'))
                    * ($commission / 100);
                $_commission = $commissionPerProduct;
            } else {
                $_commission = 0;
            }

            return $_commission;
        }
    }

    /**
     * @return bool
     */
    public function getAllowFixedQty()
    {
        return $this->_calculate_fixed_qty;
    }

    /**
     * set allow fixed qty
     *
     * @param bool $flag
     * @return $this
     */
    public function setAllowFixedQty($flag = false)
    {
        $this->_calculate_fixed_qty = $flag;
        return $this;
    }
}
