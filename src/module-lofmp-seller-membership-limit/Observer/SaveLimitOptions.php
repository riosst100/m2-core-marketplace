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
use Lof\MarketPlace\Model\ResourceModel\Group as ResourceModel;

class SaveLimitOptions implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * SaveLimitOptions constructor.
     * @param ResourceModel $resource
     */
    public function __construct(
        ResourceModel $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws FileSystemException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $group = $observer->getData('group');
        if ($group->getId()) {
            $controller = $observer->getData('controller');
            $request = $controller->getRequest();

            $limitProductDuration = $request->getParam('limit_product_duration');
            $limitProductDuration = $limitProductDuration == '' ? '-1' : (int) $limitProductDuration;

            $limitAuctionDuration = $request->getParam('limit_auction_duration');
            $limitAuctionDuration = $limitAuctionDuration == '' ? '-1' : (int) $limitAuctionDuration;

            $trialDays = $request->getParam('trial_days') ?: 0;

            $group->setData('limit_product_duration', $limitProductDuration);
            $group->setData('limit_auction_duration', $limitAuctionDuration);
            $group->setData('trial_days', $trialDays);

            $this->resource->save($group);
        }
        return $this;
    }
}
