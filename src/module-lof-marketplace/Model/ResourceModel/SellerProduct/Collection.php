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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\ResourceModel\SellerProduct;

use \Lof\MarketPlace\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var bool
     */
    protected $_joinEavFlag = false;

    /**
     * @var string|int
     */
    protected $_joinField = "entity_id";

    /**
     * @var int
     */
    protected $_entityTypeId = 4;

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Lof\MarketPlace\Model\SellerProduct::class,
            \Lof\MarketPlace\Model\ResourceModel\SellerProduct::class
        );
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }

    /**
     * Retrieve clear select
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getClearSelect()
    {
        return $this->_buildClearSelect();
    }

    /**
     * Add filter by store for seller's products
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Build clear select
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _buildClearSelect($select = null)
    {
        if (null === $select) {
            $select = clone $this->getSelect();
        }
        $select->reset(
            \Magento\Framework\DB\Select::ORDER
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_COUNT
        );
        $select->reset(
            \Magento\Framework\DB\Select::LIMIT_OFFSET
        );
        $select->reset(
            \Magento\Framework\DB\Select::COLUMNS
        );

        return $select;
    }

    /**
     * Retrieve all  Assign Products for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllAssignProducts($condition, $limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('entity_id');
        $idsSelect->where($condition);
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * Retrieve all entity_id for collection
     *
     * @param int|string $limit
     * @param int|string $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        $idsSelect->columns('entity_id');
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * @param $condition
     * @param $attributeData
     * @return int
     */
    public function setProductData($condition, $attributeData)
    {
        return $this->getConnection()->update(
            $this->getTable('lof_marketplace_product'),
            $attributeData,
            $condition
        );
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function joinEavAttributeTable()
    {
        if (!$this->_joinEavFlag) {
            $product = $this->getTable('catalog_product_entity_varchar');
            $eav_attribute = $this->getTable('eav_attribute');
            $this->getSelect()->join(
                $product . ' as abc',
                'main_table.product_id = abc.'.$this->_joinField,
                [
                    "name" => "value",
                ]
            )->join(
                $eav_attribute . ' as a',
                'a.attribute_id = abc.attribute_id',
                []
            )->where('abc.store_id = 0 AND a.attribute_code = "name" AND a.entity_type_id = '.(int)$this->_entityTypeId);

            $this->_joinEavFlag = true;
        }
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinEavAttributeTable();
        parent::_renderFiltersBefore();
    }
}
