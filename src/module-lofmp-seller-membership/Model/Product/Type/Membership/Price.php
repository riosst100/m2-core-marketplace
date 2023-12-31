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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Model\Product\Type\Membership;

class Price extends \Magento\Catalog\Model\Product\Type\Price
{

    /**
     * Get product final price
     *
     * @param   float $qty
     * @param   \Magento\Catalog\Model\Product $product
     * @return  float
     */
    public function getFinalPrice($qty, $product)
    {
        return parent::getFinalPrice($qty, $product);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($product)
    {
        $price = 0;

        $duration = $product->getData('seller_duration');

        if (!$duration) {
            $duration = $product->load($product->getId())
                            ->getData('seller_duration');
        }
        if ($duration && !is_array($duration)) {
            $duration = @json_decode($duration, true);
            $price = @current($duration);
            $price = $price['membership_price'];
        } elseif (is_array($duration)) {
            $price = @current($duration);
            $price = $price['membership_price'];
        }

        $product->setData('price', $price);

         return parent::getPrice($product);
    }

    /**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Product $product
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($product, $qty = null)
    {
        $membership = $product->getCustomOption('seller_duration');

        if (!$membership) {
            return parent::getBasePrice($product, $qty);
        }

        $membership = @unserialize($membership->getValue());

        $price = isset($membership['membership_price']) ? $membership['membership_price'] : 0;

        return $price;
    }
}
