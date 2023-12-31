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

interface SellersFrontendProductRepositoryInterface
{
    /**
     * Retrieve Seller Products matching the specified criteria.
     * @param int $seller_id
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        $seller_id,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Seller Products matching the specified criteria.
     * @param string $sellerUrl
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Lof\MarketPlace\Api\Data\SellerProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerProductsList(
        string $sellerUrl,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );
}
