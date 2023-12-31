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

interface SellerVacationRepositoryInterface
{
    /**
     * GET seller vacation - the url key of seller. ex: sellerA
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Api\Data\SellerVacationInterface
     */
    public function getSellerVacation(string $sellerUrl);

    /**
     * GET seller vacation by sellerId
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\SellerVacationInterface
     */
    public function getSellerVacationById(int $sellerId);

    /**
     * PUT Vacation
     * @param int $customerId
     * @param \Lof\MarketPlace\Api\Data\SellerVacationInterface $vacation
     * @return \Lof\MarketPlace\Api\Data\SellerVacationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function putSellerVacation(int $customerId, \Lof\MarketPlace\Api\Data\SellerVacationInterface $vacation);

    /**
     * Retrieve Vacation of logged in customer
     * @param int $customerId
     * @return \Lof\MarketPlace\Api\Data\SellerVacationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(int $customerId);
}
