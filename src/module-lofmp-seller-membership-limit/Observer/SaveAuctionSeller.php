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

namespace Lofmp\SellerMembershipLimit\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveAuctionSeller implements ObserverInterface
{
    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * SaveProductSeller constructor.
     * @param \Lofmp\SellerMembership\Model\Membership $membership
     */
    public function __construct(
        \Lofmp\SellerMembership\Model\Membership $membership
    ) {
        $this->membership = $membership;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $auction = $observer->getEvent()->getAuction();
        if (!$auction || !$auction->isObjectNew()) {
            return $this;
        }

        $sellerId = $auction->getSellerId();
        if ($sellerId && $sellerId > 0) {
            $membership = $this->membership->getCollection()
                ->addFieldToFilter('seller_id', $sellerId)
                ->getFirstItem();

            if (count($membership->getData()) > 0) {
                $limitAuctionDuration = $membership->getData('limit_auction_duration');
                if ($limitAuctionDuration != -1 && $limitAuctionDuration != 0) {
                    $membership->setData('limit_auction_duration', $limitAuctionDuration - 1);
                    $membership->save();
                }
            }
        }

        return $this;
    }
}
