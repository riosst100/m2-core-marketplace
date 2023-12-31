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
namespace Lofmp\StoreLocator\Model\ResourceModel;

class StoreLocator extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context
     * @param \Magento\Store\Model\StoreManagerInterface
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lofmp_storelocator_storelocator', 'storelocator_id');
    }

    /**
     * Process block data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Cms\Model\ResourceModel\Page
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['storelocator_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lofmp_storelocator_storelocator'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }

    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if($stores = $object->getStores()){
            $table = $this->getTable('lofmp_storelocator_storelocator_store');
            $where = ['storelocator_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($stores) {
                $data = [];
                if(!is_array($stores)){
                    $temp = $stores;
                    $stores=array($temp );
                }
              
                foreach ($stores as $storeId) {
                    $data[] = ['storelocator_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
                }
                try{
                    $this->getConnection()->insertMultiple($table, $data);
                }catch(\Exception $e){
                    die($e->getMessage());
                }
            }
        }

        if($tags = $object->getTag()){
            $table = $this->getTable('lofmp_storelocator_storelocator_tag');
            $where = ['storelocator_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($tags) {
                $data = [];
                foreach ($tags as $tagId) {
                    $data[] = ['storelocator_id' => (int)$object->getId(), 'tag_id' => (int)$tagId];
                }
                try{
                    $this->getConnection()->insertMultiple($table, $data);
                }catch(\Exception $e){
                    die($e->getMessage());
                }
            }
        }

         if($categories = $object->getCategory()){
            $table = $this->getTable('lofmp_storelocator_storelocator_category');
            $where = ['storelocator_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($categories) {
                $data = [];
                foreach ($categories as $categoryId) {
                    $data[] = ['storelocator_id' => (int)$object->getId(), 'category_id' => (int)$categoryId];
                }
                try{
                    $this->getConnection()->insertMultiple($table, $data);
                }catch(\Exception $e){
                    die($e->getMessage());
                }
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
   
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
        }
        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lofmp_storelocator_storelocator'))
            ->where(
                'storelocator_id = '.(int)$id
                );
            $posts = $connection->fetchAll($select);
            $object->setData('posts', $posts);
        }
        return parent::_afterLoad($object);
    }
    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Block $object
     * @return \Magento\Framework\DB\Select
     */
    
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), \Magento\Store\Model\Store::DEFAULT_STORE_ID];

            $select->join(
                ['cbs' => $this->getTable('lofmp_storelocator_storelocator_store')],
                $this->getMainTable() . '.storelocator_id = cbs.storelocator_id',
                ['store_id']
                )->where(
                'is_active = ?',
                1
                )->where(
                'cbs.store_id in (?)',
                $stores
                )->order(
                'store_id DESC'
                )->limit(
                1
                );
            }

            return $select;
        }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->getConnection()->select()->from(
            ['cb' => $this->getMainTable()]
            )->join(
            ['cbs' => $this->getTable('lofmp_storelocator_storelocator_store')],
            'cb.storelocator_id = cbs.storelocator_id',
            []
            )->where(
            'cb.identifier = ?',
            $object->getData('identifier')
            )->where(
            'cbs.store_id IN (?)',
            $stores
            );

            if ($object->getId()) {
                $select->where('cb.storelocator_id <> ?', $object->getId());
            }
            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;
        }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lofmp_storelocator_storelocator_store'),
            'store_id'
            )->where(
            'storelocator_id = :storelocator_id'
            );

            $binds = [':storelocator_id' => (int)$id];

            return $connection->fetchCol($select, $binds);
        }
    }