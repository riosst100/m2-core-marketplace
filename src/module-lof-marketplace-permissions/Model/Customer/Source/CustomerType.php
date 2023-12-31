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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\MarketPermissions\Model\Customer\Source;

use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Option Source for customer type.
 */
class CustomerType implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getOptions() as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value
            ];
        }

        return $options;
    }

    /**
     * Get option label by value.
     *
     * @param int $value
     * @return string|null
     */
    public function getLabel($value)
    {
        $options = $this->getOptions();

        return $options[$value] ?? null;
    }

    /**
     * Get customer type options.
     *
     * @return array
     */
    private function getOptions()
    {
        return [
            SellerCustomerInterface::TYPE_SELLER_ADMIN => __('Seller admin'),
            SellerCustomerInterface::TYPE_SELLER_USER => __('Seller user'),
            SellerCustomerInterface::TYPE_INDIVIDUAL_USER => __('Individual user'),
        ];
    }
}
