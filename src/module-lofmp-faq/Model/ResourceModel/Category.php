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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Model\ResourceModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $connectionName
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
        $this->_init('lofmp_faq_category', 'category_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Lofmp\Faq\Model\Question $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

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
            ['cbs' => $this->getTable('lof_faq_category_store')],
            'cb.category_id = cbs.category_id',
            []
        )->where(
            'cb.identifier = ?',
            $object->getData('identifier')
        )->where(
            'cbs.store_id IN (?)',
            $stores
        );

        if ($object->getId()) {
            $select->where('cb.category_id <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
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
        $title = $object->getData('title');
        $category_id = $object->getData('category_id');

        if (@strtoupper(trim($title)) == 'NONE') {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Category title is invalid')
            );
        }

        $select = $this->getConnection()
                    ->select()
                    ->from('lofmp_faq_category')
                    ->where('title LIKE ?', $title)
                    ->where('category_id <> ?', $category_id);

        if ($this->getConnection()->fetchRow($select)) {
            throw new \Magento\Framework\Exception\AlreadyExistsException(
                __('A category with the same title already exists')
            );
        }

        return $this;
    }

    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['parent_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lofmp_faq_category'), $condition);

        $condition = ['category_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('lofmp_faq_question'), $condition);

        return parent::_beforeDelete($object);
    }
}
