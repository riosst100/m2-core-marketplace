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
namespace Lofmp\StoreLocator\Model\ResourceModel\StoreLocator;

use \Lofmp\StoreLocator\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'storelocator_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('lofmp_storelocator_storelocator_store', 'storelocator_id');
        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\StoreLocator\Model\StoreLocator', 'Lofmp\StoreLocator\Model\ResourceModel\StoreLocator');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('storelocator_id', 'title');
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
        $this->joinStoreRelationTable('lofmp_storelocator_storelocator_store', 'storelocator_id');
    }

    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addLinkAttributeToFilter($code, $condition)
    {
        if ($code=='storeposition') {
            $connection = $this->getConnection();
            $where      = false;
            $select = $connection->select()->from($this->getTable('lof_gallery_album_store'), 'storelocator_id');
            if (isset($condition['from'])) {
                $where = true;
                $select->where('position >= ' . (int) $condition['from']);
            }
            if (isset($condition['to'])) {
                $where = true;
                $select->where('position <= ' . (int) $condition['to']);
            }
            if (!$where) {
                $select->where('album_id = ' . (int) $condition['album_id']);
            }
            $storeIds = $connection->fetchCol($select);
            $this->getSelect()->where('storelocator_id IN (?)', $storeIds);
        }
        return $this;
    }
}
