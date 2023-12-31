<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Model\ResourceModel;

class QuickResponse extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('lofmp_rma_template', 'template_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Lofmp\Rma\Model\QuickResponse
     */
    protected function loadStoreIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Lofmp\Rma\Model\QuickResponse $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('lofmp_rma_template_store'))
            ->where('ts_template_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['ts_store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    /**
     * @param string $object
     * @return void
     */
    protected function saveStoreIds($object)
    {
        /* @var  \Lofmp\Rma\Model\QuickResponse $object */
        $condition = $this->getConnection()->quoteInto('ts_template_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('lofmp_rma_template_store'), $condition);
        foreach ((array) $object->getData('store_ids') as $id) {
            $objArray = [
                'ts_template_id' => $object->getId(),
                'ts_store_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('lofmp_rma_template_store'),
                $objArray
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\QuickResponse $object */
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\QuickResponse $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        return parent::_beforeSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\QuickResponse $object */
        if (!$object->getIsMassStatus()) {
            $this->saveStoreIds($object);
        }
        return parent::_afterSave($object);
    }

    /************************/
}
