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

namespace Lof\MarketPlace\Model\ResourceModel\Seller;

use Lof\MarketPlace\Model\ResourceModel\AbstractCollection;
use Lof\MarketPlace\Model\Seller;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'seller_id';

    /**
     * @var bool
     */
    protected $_store_filter_added = false;

    /**
     * @var
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Lof\MarketPlace\Model\Seller::class,
            \Lof\MarketPlace\Model\ResourceModel\Seller::class
        );
        $this->_map['fields']['seller_id'] = 'main_table.seller_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|seller_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray()
    {
        $res = [];
        $existingIdentifiers = [];
        foreach ($this as $item) {
            $identifier = $item->getData('url_key');

            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('seller_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $res[] = $data;
        }

        return $res;
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
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
        if (!$this->_store_filter_added) {
            $this->performAddStoreFilter($store, $withAdmin);
            $this->_store_filter_added = true;
        }
        return $this;
    }

    /**
     * Add filter by seller Id
     *
     * @param int $sellerId
     * @param bool $checkStatus
     * @return $this
     */
    public function filterBySellerId(int $sellerId, $checkStatus = true)
    {
        $this->addFieldToFilter("seller_id", $sellerId);
        if ($checkStatus) {
            $this->addFieldToFilter("status", Seller::STATUS_ENABLED);
        }
        return $this;
    }

    /**
     * Add filter by customer Id
     *
     * @param int $customerId
     * @param bool $checkStatus
     * @return $this
     */
    public function filterByCustomerId(int $customerId, $checkStatus = true)
    {
        $this->addFieldToFilter("customer_id", $customerId);
        if ($checkStatus) {
            $this->addFieldToFilter("status", Seller::STATUS_ENABLED);
        }
        return $this;
    }

    /**
     * Add filter by seller url key
     *
     * @param string $sellerUrl
     * @param bool $checkStatus
     * @return $this
     */
    public function filterBySellerUrl(string $sellerUrl, $checkStatus = true)
    {
        $this->addFieldToFilter("url_key", $sellerUrl);
        if ($checkStatus) {
            $this->addFieldToFilter("status", Seller::STATUS_ENABLED);
        }
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('lof_marketplace_store', 'seller_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('lof_marketplace_store', 'seller_id');
    }

    /**
     * @param $attribute
     * @param string $dir
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        return $this;
    }
}
