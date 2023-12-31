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

namespace Lof\MarketPlace\Api;

use Lof\MarketPlace\Api\Data\SellerInterface;
use Magento\Customer\Api\Data\CustomerInterface;

interface SellersRepositoryInterface
{
    /**
     * @param string $customerId
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentSellers($customerId);

    /**
     * @param SellerInterface $seller
     * @param int $customerId
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveProfile(SellerInterface $seller, $customerId);

    /**
     * @param SellerInterface $seller
     * @param int $customerId
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|array|mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveSeller(SellerInterface $seller, $customerId);

    /**
     * @param CustomerInterface $customer
     * @param \Lof\MarketPlace\Api\Data\RegisterSellerInterface $data
     * @param string|null $password
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|array|mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function registerNewSeller(CustomerInterface $customer, \Lof\MarketPlace\Api\Data\RegisterSellerInterface $data, $password = null);

    /**
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\RegisterSellerInterface $data
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|array|mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function becomeSeller(int $customerId, \Lof\MarketPlace\Api\Data\RegisterSellerInterface $data);

    /**
     * Retrieve Public seller profile data
     * @param int $sellerId
     * @param string $message
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPublicProfile($sellerId, $message = "");
}
