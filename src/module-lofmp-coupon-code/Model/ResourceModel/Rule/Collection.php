<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_FollowUpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Model\ResourceModel\Rule;

use \Lofmp\CouponCode\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'coupon_rule_id';
    /**
     * Define resource model
     *
     * @return void
     */


    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        // $this->getSalesRuleData('salesrule','coupon_rule_id');
        // $this->performAfterLoad('lofmp_couponcode_rule_store', 'rule_id');
        $this->getRuleAfterLoad();
        $this->getSellerAfterLoad();

        return parent::_afterLoad();
    }

    protected function _construct()
    {
        $this->_init('Lofmp\CouponCode\Model\Rule', 'Lofmp\CouponCode\Model\ResourceModel\Rule');
        // $this->_map['fields']['store'] = 'store_table.store_id';
    }

      /**
     * Returns pairs email_id - email_name
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('coupon_rule_id', 'name');
    }
    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }
    public function setRealGroupsFilter()
    {
        return $this->addFieldToFilter('coupon_rule_id', ['gt' => 0]);
    }
    protected function getRuleAfterLoad()
    {
        $items = $this->getColumnValues("coupon_rule_id");
        if (count($items)) {
            $connection = $this->getConnection();
            foreach ($this as $item) {
                $ruleId = $item->getData('rule_id');
                $select = $connection->select()->from(['salesrule' => $this->getTable('salesrule')])->where('salesrule.rule_id = (?)', $ruleId);
                $result = $connection->fetchRow($select);
                $item->setData('name',$result['name']);
                $item->setData('description',$result['description']);
                $item->setData('from_date',$result['from_date']);
                $item->setData('to_date',$result['to_date']);
                $item->setData('times_used',$result['times_used']);
                $item->setData('is_active',$result['is_active']);
                $item->setData('uses_per_customer',$result['uses_per_customer']);
                $item->setData('uses_per_coupon',$result['uses_per_coupon']);
            }
        }
    }
    protected function getSellerAfterLoad()
    {
        $items = $this->getColumnValues("seller_id");
        if (count($items)) {
            $connection = $this->getConnection();
            foreach ($this as $item) {
                $sellerId = $item->getData('seller_id');
                $select = $connection->select()->from(['seller' => $this->getTable('lof_marketplace_seller')])->where('seller.seller_id = (?)', $sellerId);
                $result = $connection->fetchRow($select);
                if ($result) {
                    $item->setData('seller_name', $result['name']);
                }
            }
        }
    }
}
