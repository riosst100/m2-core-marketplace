<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\TimeDiscount\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ProductRepositoryInterface
{
    /**
     * @param \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
     * @return mixed
     */
    public function save(
        \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
    );
    /**
     * Retrieve Product matching the specified criteria.
     * @param string $sku
     * @return \Lofmp\TimeDiscount\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBySku($sku);

    /**
     * Retrieve Product matching the specified criteria.
     * @param int $id
     * @return \Lofmp\TimeDiscount\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Retrieve Product matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\TimeDiscount\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * List Product
     * @param \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
    );

    /**
     * Delete by ID
     * @param int $sku
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteBySku($sku);

    /**
     * @param int $customerId
     * @param int $sku
     * @param \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
     * @return mixed
     */
    public function sellerSaveProduct(
        int $customerId,
        $sku,
        \Lofmp\TimeDiscount\Api\Data\ProductInterface $product
    );

    /**
     * Retrieve Chat matching the specified criteria.
     * @param int $customerId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lofmp\TimeDiscount\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerGetList(
        int $customerId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Get info about product by product SKU
     *
     * @param int $customerId
     * @param string $sku
     * @return \Lofmp\TimeDiscount\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sellerGet(int $customerId, $sku);

    /**
     * Get info about product by product SKU
     *
     * @param string $sku
     * @return \Lofmp\TimeDiscount\Api\Data\ProductDetailInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDetail($sku);

    /**
     * Delete Chat by ID
     * @param int $customerId
     * @param int $sku
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sellerDeleteBysku(int $customerId, $sku);

}

