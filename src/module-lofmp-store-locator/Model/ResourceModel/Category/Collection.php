<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Model\ResourceModel\Category;

use \Lofmp\StoreLocator\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'category_id';
    protected function _afterLoad()
    {
        $this->performAfterLoad('lofmp_storelocator_category', 'category_id');

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\StoreLocator\Model\Category', 'Lofmp\StoreLocator\Model\ResourceModel\Category');
    }

    /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'title');
    }

    /**
     * Add filter by store
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
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('lofmp_storelocator_category', 'category_id');
    }

    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addLinkCategoryToFilter($code, $condition)
    {
        if($code=='position'){
            $connection = $this->getConnection();
            $where = '';
            if(isset($condition['from'])){
                $where .= 'position >= ' . $condition['from'] . ' AND ';
            }
            if(isset($condition['to'])){
                $where .= ' position <= ' . $condition['to'] . ' AND ';
            }
            if($where!=''){
                $where .= ' category_id = ' . $condition['category_id'];
            }
            $select = 'SELECT category_id FROM ' . $this->getTable('lofmp_storelocator_storelocator_category') . ' WHERE ' . $where;
            $Ids = $connection->fetchCol($select);
            $this->getSelect()->where('category_id IN (?)', $Ids);
        }
        return $this;
    }
}
