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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;

interface SellersFrontendRepositoryInterface
{
    /**
     * get seller info by product id
     * @param int $product_id
     * @param boolean $showOtherInfo - option to show other information of this seller
     * @param boolean $getProducts - option to show seller products
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|null
     * @throws LocalizedException
     */
    public function getSellerByProductId($product_id, $showOtherInfo = false, $getProducts = false);

    /**
     * get seller info by product sku
     * @param string $sku
     * @param int|null $storeId
     * @param boolean $showOtherInfo - option to show other information of this seller
     * @param boolean $getProducts - option to show seller products
     * @return \Lof\MarketPlace\Api\Data\SellerInterface|null
     * @throws LocalizedException
     */
    public function getSellerByProductSku($sku, $storeId = null, $showOtherInfo = false, $getProducts = false);

    /**
     * @param int $sellerId - The seller ID
     * @param boolean $showOtherInfo - option to show other information of this seller
     * @param boolean $getProducts - option to show seller products
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(int $sellerId, $showOtherInfo = false, $getProducts = false);

    /**
     * get seller by url key
     *
     * @param string $sellerUrl - the url key of seller. ex: sellerA
     * @param boolean $showOtherInfo - option to show other information of this seller
     * @param boolean $getProducts - option to show seller products
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByUrl(string $sellerUrl, $showOtherInfo = false, $getProducts = false);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param boolean $showOtherInfo
     * @param boolean $getProducts - option to show seller products
     * @return \Lof\MarketPlace\Api\Data\SellersSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria,
        $showOtherInfo = false,
        $getProducts = false
    );

    /**
     * Get seller ratings by seller id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\RatingSearchResultsInterface
     */
    public function getSellersRating($sellerId);
}
