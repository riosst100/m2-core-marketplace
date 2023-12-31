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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface AuctionRepositoryInterface
 * @package Lof\Auction\Api
 */
interface AuctionRepositoryInterface
{

    /**
     * Retrieve Min Auction Amount
     * @param int $entityId
     * @param int|null $storeId
     * @return float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMinAuctionAmount($entityId, $storeId = null);

    /**
     * Retrieve Auction Product
     * @param int $customerId
     * @param int $entityId
     * @param int $productId
     * @param int|null $storeId
     * @return \Lof\Auction\Api\Data\EndAuctionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAuctionDetailAfterEnd($customerId, $entityId, $productId, $storeId = null);

}
