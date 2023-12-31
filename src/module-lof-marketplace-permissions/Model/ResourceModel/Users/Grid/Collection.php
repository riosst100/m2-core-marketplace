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

namespace Lof\MarketPermissions\Model\ResourceModel\Users\Grid;

/**
 * Class Collection.
 */
class Collection extends \Magento\Customer\Model\ResourceModel\Grid\Collection
{
    /**
     * @var array
     */
    protected $mapper = [
        'status' => 'seller_customer.status'
    ];

    /**
     * Init select.
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinAdvancedCustomerEntityTable();
        $this->joinSellerTable();
        $this->joinUserRolesTable();
        $this->joinRolesTable();
        $this->setSortOrder();

        return $this;
    }

    /**
     * Add field filter to collection.
     *
     * @param string|array $field
     * @param string|int|array|null $condition
     * @return \Lof\MarketPermissions\Model\ResourceModel\Users\Grid\Collection
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (isset($this->mapper[$field])) {
            $field = $this->mapper[$field];
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Join advanced customer entity table.
     *
     * @return void
     */
    private function joinAdvancedCustomerEntityTable()
    {
        $this->getSelect()->joinLeft(
            ['seller_customer' => $this->getTable('lof_marketplace_advanced_customer_entity')],
            'seller_customer.customer_id = main_table.entity_id',
            ['seller_customer.status']
        );
    }

    /**
     * Join seller table.
     *
     * @return void
     */
    private function joinSellerTable()
    {
        $this->getSelect()->joinLeft(
            ['marketplace_seller' => $this->getTable('lof_marketplace_seller')],
            'marketplace_seller.seller_id = seller_customer.seller_id',
            ['marketplace_seller.seller_id AS marketplace_seller_seller_id']
        );
    }

    /**
     * Join user roles table.
     *
     * @return void
     */
    private function joinUserRolesTable()
    {
        $this->getSelect()->joinLeft(
            ['user_role' => $this->getTable('lof_marketplace_user_roles')],
            'user_role.user_id = main_table.entity_id',
            ['']
        );
    }

    /**
     * Join roles table name.
     *
     * @return void
     */
    private function joinRolesTable()
    {
        $this->getSelect()->joinLeft(
            ['role' => $this->getTable('lof_marketplace_roles')],
            'user_role.role_id = role.role_id',
            ['role.role_id', 'role.role_name']
        );
    }

    /**
     * Set sort order.
     *
     * @return void
     */
    private function setSortOrder()
    {
        $this->setOrder('main_table.name', \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
    }
}
