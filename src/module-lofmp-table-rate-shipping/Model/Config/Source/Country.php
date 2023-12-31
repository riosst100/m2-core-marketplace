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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Model\Config\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Lofmp\TableRateShipping\Block\Shipping\Shipping
     */
    protected $country;

    /**
     * @param \Lofmp\TableRateShipping\Block\Shipping\Shipping $country
     */
    public function __construct(\Lofmp\TableRateShipping\Block\Shipping\Shipping $country)
    {
        $this->country = $country;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $country = $this->country->getCountryOptionArray();
        $data = [];

        foreach ($country as $_country) {
            $data[] = [
                'value' => $_country['value'],
                'label' => $_country['label']
            ];
        }

        return $data;
    }
}
