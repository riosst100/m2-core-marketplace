<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\FeaturedProducts\Api\Data;

interface FeaturedProductInterface
{
    const SELLER_ID = 'seller_id';
    const FEATURED_TO = 'featured_to';
    const ID = 'id';
    const PRODUCT_ID = 'product_id';
    const SORT_ORDER = 'sort_order';
    const FEATURED_FROM = 'featured_from';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $id
     * @return \Lofmp\FeaturedProducts\FeaturedProduct\Api\Data\FeaturedProductInterface
     */
    public function setId($id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $sellerId
     * @return \Lofmp\FeaturedProducts\FeaturedProduct\Api\Data\FeaturedProductInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get featured_from
     * @return string|null
     */
    public function getFeaturedFrom();

    /**
     * Set featured_from
     * @param string $featuredFrom
     * @return \Lofmp\FeaturedProducts\FeaturedProduct\Api\Data\FeaturedProductInterface
     */
    public function setFeaturedFrom($featuredFrom);

    /**
     * Get featured_to
     * @return string|null
     */
    public function getFeaturedTo();

    /**
     * Set featured_to
     * @param string $featuredTo
     * @return \Lofmp\FeaturedProducts\FeaturedProduct\Api\Data\FeaturedProductInterface
     */
    public function setFeaturedTo($featuredTo);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Lofmp\FeaturedProducts\FeaturedProduct\Api\Data\FeaturedProductInterface
     */
    public function setProductId($productId);

    /**
     * Get sort_order
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set sort_order
     * @param int $sortOrder
     * @return \Lofmp\FeaturedProducts\FeaturedProduct\Api\Data\FeaturedProductInterface
     */
    public function setSortOrder($sortOrder);
}

