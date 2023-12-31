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

class AuctionProductNewAction implements ObserverInterface
{
    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * AuctionProductNewAction constructor.
     * @param \Lofmp\SellerMembership\Model\Membership $membership
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->membership = $membership;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $seller = $observer->getData('seller');
        $sellerId = $seller->getData('seller_id');
        if ($sellerId && $sellerId > 0) {
            $membership = $this->membership->getCollection()
                ->addFieldToFilter('seller_id', $sellerId)
                ->getFirstItem();

            if (count($membership->getData()) > 0) {
                $limitAuctionDuration = $membership->getData('limit_auction_duration');
                if ($limitAuctionDuration == 0) {
                    $redirectionUrl = $this->url->getUrl('lofmpauction/product/');
                    $this->responseFactory->create()->setRedirect($redirectionUrl)->sendResponse();
                }
            }
        }

        return $this;
    }
}
