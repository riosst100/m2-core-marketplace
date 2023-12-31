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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Model\Rule\Condition;

class SellerAttributes
{
    /**
     * @return array
     */
    public function getSellerAllAttributes(): array
    {
        return [
            'company' => __('Company'),
            'company_locality' => __('Company Locality'),
            'address' => __('Address'),
            'email' => __('Email'),
            'city' => __('City'),
            'country_id' => __('Country'),
            'postcode' => __('Zip/Postal Code'),
            'region' => __('State/Province'),
            'telephone' => __('Phone Number'),
            'verify_status' => __('Verify Status'),
            'status' => __('Status'),
            'created_at' => __('Created At'),
            'store_id' => __('Store Id')
        ];
    }

    /**
     * @return array
     */
    public function getSellerUsage(): array
    {
        return [
            'product_count' => __('Number of Products'),
            'sale' => __('Number of Completed Sales'),
            'total_sold' => __('Total amount sold')
        ];
    }
}
