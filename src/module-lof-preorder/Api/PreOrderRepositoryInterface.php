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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Api;

interface PreOrderRepositoryInterface
{

    /**
     * Retrieve PreOrder status of product
     * @param int $productId
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIsPreorder($productId);

    /**
     * Retrieve PreOrder note of product
     * @param int $productId
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPreorderNote($productId);


    /**
     * Add Custom Price For Product in Quote
     * @param int $quoteId
     * @param int $quoteItemId
     * @param int $productId
     * @param int $storeId
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addCustomPrice($quoteId, $quoteItemId, $productId, $storeId);

    /**
     * Create Complete Preorder
     * @param int $customerId
     * @param int $productId
     * @param int $storeId
     * @param int $itemId
     * @param int $preProductId
     * @param int $orderId
     * @param int $qty
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createCompletePreorderQuote($customerId, $productId, $storeId, $itemId, $preProductId, $orderId, $qty);

    /**
     * Retrieve PreOrder complete order
     * @param int $orderId
     * @return bool|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function completeOrder($orderId);

    /**
     * Retrieve PreOrder
     * @param int|null $itemId
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function notifyPreorder($itemId);

    /**
     * Retrieve PreOrder matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\PreOrder\Api\Data\PreOrderSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Received Staff module settings
     * @param string $path
     * @return mixed
     */
    public function getSetting($path);
}
