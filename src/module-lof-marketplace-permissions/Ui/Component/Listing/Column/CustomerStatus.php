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

namespace Lof\MarketPermissions\Ui\Component\Listing\Column;

use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;

class CustomerStatus extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $item[$fieldName] = $this->setStatusLabel($item[$fieldName]);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Set status label.
     *
     * @param int $key
     * @return string
     */
    protected function setStatusLabel($key)
    {
        $labels = [
            SellerCustomerInterface::STATUS_ACTIVE => __('Active'),
            SellerCustomerInterface::STATUS_INACTIVE => __('Inactive'),
        ];

        return $labels[$key];
    }
}
