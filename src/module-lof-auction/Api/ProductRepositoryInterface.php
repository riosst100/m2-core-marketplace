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
 * Interface ProductRepositoryInterface
 * @package Lof\Auction\Api
 */
interface ProductRepositoryInterface
{
    /**
     * Save Product
     * @param \Lof\Auction\Api\Data\ProductInterface $product
     * @return \Lof\Auction\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Lof\Auction\Api\Data\ProductInterface $product);

    /**
     * Retrieve Product
     * @param int $entityId
     * @param \Lof\Auction\Api\Data\ProductInterface $product
     * @return \Lof\Auction\Api\Data\ProductInterface
     * @throws LocalizedException
     */
    public function update($entityId, \Lof\Auction\Api\Data\ProductInterface $product);

    /**
     * Retrieve Product
     * @param string $entityId
     * @return \Lof\Auction\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Retrieve Auction Product
     * @param string $sku
     * @param int|null $storeId
     * @return \Lof\Auction\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByProductSku($sku, $storeId = null);

    /**
     * Retrieve Product matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\Auction\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Product
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($entityId);

    /**
     * Delete Product by ID
     * @param int $entityId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($entityId);
}
