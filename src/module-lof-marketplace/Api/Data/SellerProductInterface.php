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

namespace Lof\MarketPlace\Api\Data;

interface SellerProductInterface
{
    const SELLER_PRODUCT = 'seller_product';
    const ENTITY_ID = 'entity_id';
    const PRODUCT_ID = 'product_id';
    const SELLER_ID = 'seller_id';
    const PRODUCT_NAME = 'product_name';
    const STORE_ID = 'store_id';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const COMMISSION = 'commission';
    const ADDMINASSIGN = 'adminassign';

    /**
     * Get seller_product
     * @return string|null
     */
    public function getSellerProduct();

    /**
     * Set seller_product
     * @param string $seller_product
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setSellerProduct($product);

    /**
     * Get entity_id
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param int $entity_id
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setEntityId($entity_id);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setProductId($product_id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setSellerId($seller_id);

    /**
     * Get store_id
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param int $store_id
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setStoreId($store_id);

    /**
     * Get product_name
     * @return string|null
     */
    public function getProductName();

    /**
     * Set product_name
     * @param string $product_name
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setProductName($product_name);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setUpdatedAt($updated_at);

    /**
     * Get commission
     * @return float|int|null
     */
    public function getCommission();

    /**
     * Set commission
     * @param float|int $commission
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setCommission($commission);

    /**
     * Get adminassign
     * @return float|int|null
     */
    public function getAdminassign();

    /**
     * Set adminassign
     * @param float|int $adminassign
     * @return \Lof\MarketPlace\Api\Data\SellerProductInterface
     */
    public function setAdminassign($adminassign);
}
