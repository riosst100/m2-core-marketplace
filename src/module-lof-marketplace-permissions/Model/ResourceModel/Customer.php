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

namespace Lof\MarketPermissions\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Customer mysql resource.
 */
class Customer extends AbstractDb
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_marketplace_advanced_customer_entity', 'customer_id');
    }

    /**
     * Get Customers by seller ID.
     *
     * @param int $sellerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerIdsBySellerId($sellerId)
    {
        $connection = $this->getConnection();
        $data = $connection->fetchAll(
            $connection->select()->from(
                ['ac' => $this->getMainTable()],
                ['ac.seller_id']
            )->where(
                'ac.seller_id = ?',
                $sellerId
            )
        );

        return array_map(
            function ($row) {
                return $row['customer_id'];
            },
            $data
        );
    }

    /**
     * Get Customer extension attributes.
     *
     * @param int $customerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerExtensionAttributes($customerId)
    {
        $connection = $this->getConnection();
        if ($data = $connection->fetchRow(
            $connection->select()->from(
                ['ac' => $this->getMainTable()]
            )->where(
                'ac.customer_id = ?',
                $customerId
            )->limit(
                1
            )
        )
        ) {
            return $data;
        }
        return [];
    }

    /**
     * Save custom attributes.
     *
     * @param SellerCustomerInterface $customerExtension
     * @return $this
     * @throws CouldNotSaveException|\Magento\Framework\Exception\LocalizedException
     */
    public function saveAdvancedCustomAttributes(
        SellerCustomerInterface $customerExtension
    ) {
        $customerExtensionData = $this->_prepareDataForSave($customerExtension);
        if ($customerExtensionData) {
            try {
                $this->getConnection()->insertOnDuplicate(
                    $this->getMainTable(),
                    $customerExtensionData,
                    array_keys($customerExtensionData)
                );
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('There was an error saving custom attributes.'));
            }
        }
        return $this;
    }
}
