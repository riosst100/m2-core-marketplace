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

namespace Lofmp\SellerMembership\Block\Product\View\Type;

use Lofmp\SellerMembership\Model\Source\DurationUnit;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Membership extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils   $arrayUtils
     * @param array                                  $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        parent::__construct($context, $arrayUtils, $data);
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
                $label = $duration == 1 ? __('%1 Day', $duration) : __('%1 Days', $duration);
                break;
            case DurationUnit::DURATION_WEEK:
                $label = $duration == 1 ? __('%1 Week', $duration) : __('%1 Weeks', $duration);
                break;
            case DurationUnit::DURATION_MONTH:
                $label = $duration == 1 ? __('%1 Month', $duration) : __('%1 Months', $duration);
                break;
            case DurationUnit::DURATION_YEAR:
                $label = $duration == 1 ? __('%1 Year', $duration) : __('%1 Years', $duration);
                break;
        }

        return $label;
    }

    /**
     * Get option JSON.
     *
     * @return string
     */
    public function getOptionsJSON()
    {
        $options = [];
        $durationOptions = $this->getProduct()->getData('seller_duration');

        foreach ($durationOptions as $option) {
            $options[] = [
                'label' => $this->getDurationLabel($option['membership_duration'], $option['membership_unit']),
                'value' => $option['membership_duration'].'|'.$option['membership_unit'],
                'price' => $this->convertPrice($option['membership_price']),
            ];
        }

        return json_encode($options);
    }

    /**
     * Has options.
     *
     * @return bool
     */
    public function hasOptions()
    {
        return $this->getProduct()->getTypeInstance()->hasOptions($this->getProduct());
    }

    /**
     * Format price.
     *
     * @param int    $number
     * @param string $includeContainer
     */
    public function formatPrice($number, $includeContainer = false)
    {
        return $this->priceCurrency->format($number, $includeContainer);
    }

    /**
     * Convert the base currency price to current currency.
     *
     * @param float $amount
     *
     * @return float
     */
    public function convertPrice($amount = 0)
    {
        return $this->priceCurrency->convert($amount);
    }

    /**
     * Format price to base currency.
     *
     * @param number $amount
     *
     * @return string
     */
    public function formatBasePrice($amount = 0)
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->formatPrecision($amount, 2, [], false);
    }
}
