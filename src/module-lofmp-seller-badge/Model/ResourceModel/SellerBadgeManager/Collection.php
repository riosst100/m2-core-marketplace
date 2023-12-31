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

namespace Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager;

class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'manager_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Lofmp\SellerBadge\Model\SellerBadgeManager::class,
            \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager::class
        );
    }

    /**
     * @param mixed|int|string $sellerId
     * @return $this
     */
    public function addBadgeBySellerId($sellerId)
    {
        if (!is_array($sellerId)) {
            $sellerId = [(int)$sellerId];
        }
        $this->getSelect()->join(
            ['lsb' => $this->getTable('lofmp_sellerbadge_badge')],
            "main_table.badge_id = lsb.badge_id",
            [
                'lsb.image as image',
                'lsb.name as badge_name',
                'lsb.rank as rank',
                'lsb.description as description',
                'lsb.is_active as is_active'
            ]
        )->where('main_table.seller_id IN (?)', $sellerId)
            ->where('lsb.is_active = ?', 1)
            ->order('lsb.rank ASC');
        return $this;
    }
}
