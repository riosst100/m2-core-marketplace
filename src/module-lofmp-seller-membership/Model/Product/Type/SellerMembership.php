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

namespace Lofmp\SellerMembership\Model\Product\Type;

use Lofmp\SellerMembership\Model\Source\DurationUnit;

class SellerMembership extends \Magento\Catalog\Model\Product\Type\Virtual
{
     /**
     * Product type code.
     */
    const TYPE_CODE = 'seller_membership';

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare options belonging to specific product type.
     *
     * @param \Magento\Framework\DataObject  $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $processMode
     *
     * @return array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {

        $options = $buyRequest->getData('seller_membership');

        $duration = isset($options['duration']) ? $options['duration'] : 0;

        if (!$duration) {
            return __('You need to choose options for your item.')->render();
        }
        list($duration, $durationUnit) = explode('|', $duration);

        $durationOptions = $product->getData('seller_duration');
        if (!is_array($durationOptions)) {
            $durationOptions = @json_decode($durationOptions, true);
        }

        $packagePrice = 0;
        foreach ($durationOptions as $option) {
            if ($duration == $option['membership_duration'] && $durationUnit == $option['membership_unit']) {
                $packagePrice = $option['membership_price'];
            }
        }

        $options['membership_duration'] = $duration;
        $options['membership_unit'] = $durationUnit;
        $options['seller_group'] = $product->getData('seller_group');
        $options['membership_price'] = $packagePrice;

        $product->addCustomOption('seller_duration', @serialize($options));

        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }

    /**
     * Get duration label.
     *
     * @param int $duration
     * @param int $unit
     */
    public function getDurationLabel($duration, $unit)
    {
        $label = '';
        switch ($unit) {
            case DurationUnit::DURATION_DAY:
                $label = $duration == 1 ? __('%1 day', $duration) : __('%1 days', $duration);
                break;
            case DurationUnit::DURATION_WEEK:
                $label = $duration == 1 ? __('%1 week', $duration) : __('%1 weeks', $duration);
                break;
            case DurationUnit::DURATION_MONTH:
                $label = $duration == 1 ? __('%1 month', $duration) : __('%1 months', $duration);
                break;
            case DurationUnit::DURATION_YEAR:
                $label = $duration == 1 ? __('%1 year', $duration) : __('%1 years', $duration);
                break;
        }

        return $label;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);
        if ($attributesOption = $product->getCustomOption('seller_membership')) {
            $data = @unserialize($attributesOption->getValue());
            $options['seller_membership'] = $data;
            $options['attributes_info'] = [
                ['label' => __('Duration').'', 'value' => $this->getDurationLabel($data['duration'], $data['membership_unit']).''],
            ];
        }

        return $options;
    }

    /**
     * Return true if product has options.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function hasOptions($product)
    {
        $duration = $product->getData('seller_duration');

        return (is_array($duration) && (sizeof($duration) >= 1)) || $product->getHasOptions();
    }
}
