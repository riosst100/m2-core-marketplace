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

namespace Lof\MarketPermissions\Plugin\Seller\ResourceModel\Seller;

use Lof\MarketPlace\Model\ResourceModel\Seller\Collection;
use Magento\Framework\DB\Select;

class CollectionPlugin
{

    /**
     * @var \Lof\MarketPermissions\Api\StatusServiceInterface
     */
    private $moduleConfig;

    /**
     * @var \Lof\MarketPermissions\Model\SellerContext
     */
    private $sellerContext;

    /**
     * CollectionPlugin constructor.
     * @param \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig
     */
    public function __construct(
        \Lof\MarketPermissions\Api\StatusServiceInterface $moduleConfig,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->sellerContext = $sellerContext;
    }

    /**
     * Around addFieldToFilter plugin.
     *
     * @param Collection $subject
     * @param \Closure $proceed
     * @param string|array $field
     * @param null|string|array $condition [optional]
     * @return Collection
     * @throws \Zend_Db_Select_Exception
     */
    public function aroundAddFieldToFilter(
        Collection $subject,
        \Closure $proceed,
        $field,
        $condition = null
    ) {
        if (!$this->moduleConfig->isActive()) {
            return $proceed($field, $condition);
        }

        if ($field == "status") {
            $field = "main_table.status";
        }

        if ($field != \Lof\MarketPermissions\Api\Data\SellerInterface::CUSTOMER_ID) {
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

        // TODO:
        if ($field == 'customer_id') {
            $fieldName = $subject->getConnection()->quoteIdentifier('seller_customer.customer_id');
            $condition = $subject->getConnection()->prepareSqlCondition($fieldName, $condition);
            $subject->getSelect()->where($condition, null, Select::TYPE_CONDITION);
        } else {
            $subject->getSelect()->where($condition, null, Select::TYPE_CONDITION);
        }

        return $subject;
    }

    /**
     * @return array[]
     */
    private function getAdditionalTables()
    {
        return [
            'lof_marketplace_advanced_customer_entity' => [
                'alias' => 'seller_customer',
                'condition' => 'seller_customer.seller_id = main_table.seller_id',
                'columns' => [
                    'seller_customer.seller_id',
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
     * @throws \Zend_Db_Select_Exception
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
