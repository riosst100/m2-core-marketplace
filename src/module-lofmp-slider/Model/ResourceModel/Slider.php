<?php

namespace Lofmp\Slider\Model\ResourceModel;

class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_storeManager;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
        ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init('lofmp_marketplace_slider', 'slider_id');
    }

    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['slider_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lofmp_marketplace_slider'), $condition);
        return parent::_beforeDelete($object);
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this;
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if($slider = $object->getStores()){
            $table = $this->getTable('lofmp_marketplace_slider_store');
            $where = ['slider_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);
            if ($slider) {
                $data = [];
                foreach ($slider as $slideRid) {
                    $data[] = ['slider_id' => (int)$object->getId(), 'store_id' => (int)$slideRid];
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

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $slider = $this->lookupSliderIds($object->getId());
            $object->setData('store_id', $slider);
            $object->setData('slider', $slider);
        }
        if ($id = $object->getId()) {
            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('lofmp_marketplace_slider'))
            ->where(
                'slider_id = '.(int)$id
                );
            $slider = $connection->fetchAll($select);
            $object->setData('slider', $slider);
        }
        return parent::_afterLoad($object);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
            return $select;
        }

    public function lookupSliderIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lofmp_marketplace_slider'),
            'slider_id'
            )->where(
            'slider_id = :slider_id'
            );

            $binds = [':slider_id' => (int)$id];
        return $connection->fetchCol($select, $binds);
    }
}