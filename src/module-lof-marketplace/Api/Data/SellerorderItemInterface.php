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

interface SellerorderItemInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    const ID = 'id';
    const ITEM_ID = 'item_id';
    const ORDER_ID = 'order_id';
    const ENTITY_ID = 'entity_id';
    const PRODUCT_ID = 'product_id';
    const STATUS = 'status';
    const QUANTITY = 'quantity';
    const DESCRIPTION = 'description';
    const NAME = 'name';
    const SKU = 'sku';
    const PRODUCT_TYPE = 'product_type';
    const PRICE = 'price';
    const BASE_PRODUCT_PRICE = 'base_product_price';
    const OPTIONS = 'options';
    const CREATED_AT = 'created_at';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $item_idid
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setId($id);

    /**
     * Get order_id
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param int $order_id
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setOrderId($order_id);

    /**
     * Get item_id
     * @return int|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param int $item_id
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setItemId($item_id);

    /**
     * Get entity_id
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param int $entity_id
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
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
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setProductId($product_id);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setStatus($status);

    /**
     * Get base_product_price
     * @return float|int
     */
    public function getBaseProductPrice();

    /**
     * Set base_product_price
     * @param float|int $base_product_price
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setBaseProductPrice($base_product_price);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get options
     * @return string|null
     */
    public function getOptions();

    /**
     * Set options
     * @param string $options
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setOptions($options);

    /**
     * Get quantity
     * @return int
     */
    public function getQuantity();

    /**
     * Set quantity
     * @param int $quantity
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setQuantity($quantity);

    /**
     * Get description
     * @return string
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setDescription($description);

    /**
     * Get name
     * @return string
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setName($name);

    /**
     * Get sku
     * @return string
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setSku($sku);

    /**
     * Get product_type
     * @return string
     */
    public function getProductType();

    /**
     * Set product_type
     * @param string $product_type
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setProductType($product_type);

    /**
     * Get price
     * @return float|int
     */
    public function getPrice();

    /**
     * Set price
     * @param float|int $price
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemInterface
     */
    public function setPrice($price);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketPlace\Api\Data\SellerorderItemExtensionInterface $extensionAttributes
     * @return \Lof\MarketPlace\Api\Data\SellerInterface
     */
    public function setExtensionAttributes(
        \Lof\MarketPlace\Api\Data\SellerorderItemExtensionInterface $extensionAttributes
    );
}
