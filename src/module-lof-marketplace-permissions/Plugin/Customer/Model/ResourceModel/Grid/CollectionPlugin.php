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

namespace Lof\MarketPermissions\Plugin\Customer\Model\ResourceModel\Grid;

use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Customer\Model\ResourceModel\Grid\Collection;
use Magento\Framework\DB\Select;

/**
 * Plugin for customer grid collection.
 */
class CollectionPlugin
{
    /**
     * @var string
     */
    private $customerTypeExpressionPattern = '(IF(seller_customer.seller_id > 0, '
    . 'IF(seller_customer.customer_id = seller.customer_id, "%d", "%d"), "%d"))';

    /**
     * @var array
     */
    private $expressionFields = [
        'customer_type'
    ];

    /**
     * Before loadWithFilter plugin.
     *
     * @param Collection $subject
     * @param bool $printQuery [optional]
     * @param bool $logQuery [optional]
     * @return array
     */
    public function beforeLoadWithFilter(
        Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        foreach ($this->getAdditionalTables() as $tableName => $tableParam) {
            $this->joinAdditionalTable(
                $subject->getSelect(),
                $subject->getTable($tableName),
                $tableParam['alias'],
                $tableParam['condition'],
                $tableParam['columns']
            );
        }

        return [$printQuery, $logQuery];
    }

    /**
     * Around addFieldToFilter plugin.
     *
     * @param Collection $subject
     * @param \Closure $proceed
     * @param string|array $field
     * @param null|string|array $condition [optional]
     * @return Collection
     */
    public function aroundAddFieldToFilter(
        Collection $subject,
        \Closure $proceed,
        $field,
        $condition = null
    ) {
        $fieldMap = $this->getFilterFieldsMap();
        $fieldName = $fieldMap['fields'][$field] ?? null;
        if (!$fieldName) {
            return $proceed($field, $condition);
        }

        foreach ($this->getAdditionalTables() as $tableName => $tableParam) {
            $this->joinAdditionalTable(
                $subject->getSelect(),
                $subject->getTable($tableName),
                $tableParam['alias'],
                $tableParam['condition'],
                $tableParam['columns']
            );
        }

        if (!\in_array($field, $this->expressionFields, true)) {
            $fieldName = $subject->getConnection()->quoteIdentifier($fieldName);
        }
        $condition = $subject->getConnection()->prepareSqlCondition($fieldName, $condition);
        $subject->getSelect()->where($condition, null, Select::TYPE_CONDITION);

        return $subject;
    }

    /**
     * Get map for filterable fields.
     *
     * @return array
     */
    private function getFilterFieldsMap()
    {
        return [
            'fields' => [
                'email' => 'main_table.email',
                'status' => 'seller_customer.status',
                'seller_name' => 'seller.name',
                'customer_type' => $this->prepareCustomerTypeColumnExpression()
            ]
        ];
    }

    /**
     * Prepare expression for customer type column.
     *
     * @return string
     */
    private function prepareCustomerTypeColumnExpression()
    {
        return sprintf(
            $this->customerTypeExpressionPattern,
            SellerCustomerInterface::TYPE_SELLER_ADMIN,
            SellerCustomerInterface::TYPE_SELLER_USER,
            SellerCustomerInterface::TYPE_INDIVIDUAL_USER
        );
    }

    /**
     * Get additional tables.
     *
     * @return array
     */
    private function getAdditionalTables(): array
    {
        return [
            'lof_marketplace_advanced_customer_entity' => [
                'alias' => 'seller_customer',
                'condition' => 'seller_customer.customer_id = main_table.entity_id',
                'columns' => [
                    'seller_customer.status',
                ],
            ],
            'lof_marketplace_seller' => [
                'alias' => 'seller',
                'condition' => 'seller.seller_id = seller_customer.seller_id',
                'columns' => [
                    'seller_name' => 'seller.name',
                    'customer_type' => new \Zend_Db_Expr($this->prepareCustomerTypeColumnExpression()),
                ],
            ],
        ];
    }

    /**
     * Join additional table to select.
     *
     * @param Select $select
     * @param string $tableName
     * @param string $tableAlias
     * @param string $condition
     * @param array $columns
     * @return void
     */
    private function joinAdditionalTable(
        Select $select,
        string $tableName,
        string $tableAlias,
        string $condition,
        array $columns
    ) {
        $usedTables = array_column($select->getPart(Select::FROM), 'tableName');
        if (!\in_array($tableName, $usedTables, true)) {
            $select->joinLeft([$tableAlias => $tableName], $condition, $columns);
        }
    }
}
