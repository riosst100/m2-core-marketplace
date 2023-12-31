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
 * @package    SellerMembershipLimit
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembershipLimit\Observer;

use Magento\Framework\Exception\FileSystemException;

class SaveDefaultMembership implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws FileSystemException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $group = $observer->getData('group');
        $membership = $observer->getData('membership');
        $limitProductDuration = $group->getData('limit_product_duration');
        $limitProductDuration = $limitProductDuration == '' ? '-1' : (int) $limitProductDuration;

        $limitAuctionDuration = $group->getData('limit_auction_duration');
        $limitAuctionDuration = $limitAuctionDuration == '' ? '-1' : (int) $limitAuctionDuration;

        $membership->setData('limit_product_duration', $limitProductDuration);
        $membership->setData('limit_auction_duration', $limitAuctionDuration);
        $membership->save();

        return $this;
    }
}
