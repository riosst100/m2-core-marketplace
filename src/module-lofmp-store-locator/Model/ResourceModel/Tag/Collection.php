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
namespace Lofmp\StoreLocator\Model\ResourceModel\Tag;

use \Lofmp\StoreLocator\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'tag_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('lofmp_storelocator_tag', 'tag_id');

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\StoreLocator\Model\Tag', 'Lofmp\StoreLocator\Model\ResourceModel\Tag');
    }

    /**
     * Returns pairs tag_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('tag_id', 'title');
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
        $this->joinStoreRelationTable('lofmp_storelocator_tag', 'tag_id');
    }
        /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addLinkTagToFilter($code, $condition)
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
                $where .= ' tag_id = ' . $condition['tag_id'];
            }
            $select = 'SELECT tag_id FROM ' . $this->getTable('lofmp_storelocator_tag') . ' WHERE ' . $where;
            $Ids = $connection->fetchCol($select);
            $this->getSelect()->where('tag_id IN (?)', $Ids);
        }
        return $this;
    }
}
