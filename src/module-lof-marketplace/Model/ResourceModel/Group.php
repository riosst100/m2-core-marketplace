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

namespace Lof\MarketPlace\Model\ResourceModel;

use Lof\MarketPlace\Model\Seller;

class Group extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store model
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Store manager
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\Datetime
     */
    protected $dateTime;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Group\Collection
     */
    protected $collection;

    /**
     * Group constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lof_marketplace_group', 'group_id');
    }

    /**
     * Process group data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_marketplace_group'),
            'url_key'
        )
            ->where(
                'url_key = ?',
                $object->getUrlKey()
            )
            ->where(
                'group_id != ?',
                $object->getGroupId()
            );

        $trialDays = true;
        $groupData = $object->getData();

        if (array_key_exists('trial_days', $groupData)) {
            $trialDays = false;
        }

        $result = $connection->fetchCol($select);
        if (count($result) > 0 && $trialDays) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('URL key already exists.' . count($result))
            );
        }

        return parent::_beforeSave($object);
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && ($field === null)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Process seller data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getGroupId() == Seller::DEFAULT_GROUP_ID) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can not delete default seller group %1', $object->getGroupId())
            );
        }
        $this->updateSellerDefaultGroupId($object->getGroupId());

        return parent::_beforeDelete($object);
    }

    /**
     * Get seller ids to which specified item is assigned
     *
     * @param int $groupId
     * @return array
     */
    public function lookupSellerIds($groupId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('lof_marketplace_seller'),
            'seller_id'
        )
            ->where(
                'group_id = ?',
                (int)$groupId
            );
        return $connection->fetchCol($select);
    }

    /**
     * Get seller ids to which specified item is assigned
     *
     * @param int $groupId
     * @return void
     */
    protected function updateSellerDefaultGroupId($groupId)
    {
        $sellers = $this->lookupSellerIds($groupId);
        if ($sellers) {
            $table = $this->getTable('lof_marketplace_seller');
            foreach ($sellers as $sellerId) {
                $sellerId = is_array($sellerId) ? $sellerId["seller_id"] : $sellerId;
                $data = ['group_id' => Seller::DEFAULT_GROUP_ID];
                $this->getConnection()->update($table, $data, ['seller_id = ?' => (int)$sellerId]);
            }
        }
    }
}
