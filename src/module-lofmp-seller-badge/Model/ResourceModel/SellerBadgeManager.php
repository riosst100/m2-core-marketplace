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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Model\ResourceModel;

class SellerBadgeManager extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lofmp_sellerbadge_manager', 'manager_id');
    }

    /**
     * @param $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanByBadgeIds($ids)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface::BADGE_ID)
                ->where(\Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface::BADGE_ID . ' IN (?)', $ids),
            $this->getMainTable()
        );

        $this->getConnection()->query($query);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanAllIndex()
    {
        $this->getConnection()->delete($this->getMainTable());
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertIndexData(array $data)
    {
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);
        return $this;
    }
}
