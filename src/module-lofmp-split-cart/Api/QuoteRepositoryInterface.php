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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface QuoteRepositoryInterface
{

    /**
     * Save Quote
     * @param \Lofmp\SplitCart\Api\Data\QuoteInterface $quote
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Lofmp\SplitCart\Api\Data\QuoteInterface $quote
    );

    /**
     * Retrieve Quote
     * @param string $entityId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve Quote matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\SplitCart\Api\Data\QuoteSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * @param string|int $cartId
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSplitCart($cartId);

    /**
     * get split quote by cart id and seller url for logged in customer
     *
     * @param string|int $cartId
     * @param string $sellerUrl
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSplitCartForCustomer($cartId, $sellerUrl);

    /**
     * get split quote by cart id and seller url for guest
     *
     * @param string $cartId
     * @param string $sellerUrl
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSplitCartForGuest($cartId, $sellerUrl);

    /**
     * Delete Quote
     * @param \Lofmp\SplitCart\Api\Data\QuoteInterface $quote
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\SplitCart\Api\Data\QuoteInterface $quote
    );

    /**
     * Delete Quote by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);

    /**
     * Init Split cart
     * @param int $cartId
     * @param string|int $sellerUrl
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface|bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initSplitOrder(
        $cartId,
        $sellerUrl
    );

    /**
     * Init Split cart for guest
     * @param string $cartId
     * @param string|int $sellerUrl
     * @return \Lofmp\SplitCart\Api\Data\QuoteInterface|bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initSplitOrderGuest(
        $cartId,
        $sellerUrl
    );

    /**
     * Returns information for the cart for a specified customer.
     *
     * @param int $customerId The customer ID.
     * @param string $sellerUrl The seller url
     * @return \Magento\Quote\Api\Data\CartInterface Cart object.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified customer does not exist.
     */
    public function getCartForCustomer($customerId, $sellerUrl);

    /**
     * Enable a guest user to return information for a specified cart.
     *
     * @param string $cartId
     * @param string $sellerUrl The seller url
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCartForGuest($cartId, $sellerUrl);

    /**
     * Delete split cart for logged in customer - use same function as observer after customer logout
     *
     * @param int $cartId The Cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified customer does not exist.
     */
    public function removeSplitCart($cartId);

    /**
     * Delete split cart for guest, clear split cart for guest
     *
     * @param string $cartId The Cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified customer does not exist.
     */
    public function removeSplitCartGuest($cartId);

    /**
     * Update split cart after place order for splite quote
     *
     * @param int $cartId The Cart ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified customer does not exist.
     */
    public function updateSplitCart($cartId);

    /**
     * set seller id
     *
     * @param int $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);
}
