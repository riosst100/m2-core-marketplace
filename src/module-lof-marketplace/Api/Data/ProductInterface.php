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

interface ProductInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const TYPE_ID = 'type_id';
    const SKU = 'sku';
    const QTY = 'qty';
    const HAS_OPTIONS = 'has_options';
    const REQUIRED_OPTIONS = 'required_options';
    const APPROVAL = 'approval';
    const NAME = 'name';
    const PRICE = 'price';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const IS_IN_STOCK = 'is_in_stock';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set ID
     *
     * @param int $entity_id
     * @return $this
     */
    public function setEntityId($entity_id);

    /**
     * Get ID
     *
     * @return int|float
     */
    public function getQty();

    /**
     * Set qty
     *
     * @param int|float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Get type_id
     *
     * @return int|null
     */
    public function getTypeId();

    /**
     * Set type_id
     *
     * @param int $type_id
     * @return $this
     */
    public function setTypeId($type_id);

    /**
     * Get SKU
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return $this
     */
    public function setSku($sku);

    /**
     * Get has_options
     *
     * @return string|null
     */
    public function getHasOptions();

    /**
     * Set has_options
     *
     * @param int $has_options
     * @return $this
     */
    public function setHasOptions($has_options);

    /**
     * Get required_options
     *
     * @return string|null
     */
    public function getRequiredOptions();

    /**
     * Set required_options
     *
     * @param int $required_options
     * @return $this
     */
    public function setRequiredOptions($required_options);

    /**
     * Get created_at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $created_at
     * @return $this
     */
    public function setCreatedAt($created_at);

    /**
     * Get updated_at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param string $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at);

    /**
     * Get seller_id
     *
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     *
     * @param int $seller_id
     * @return $this
     */
    public function setSellerId($seller_id);

    /**
     * Get approval
     *
     * @return string|null
     */
    public function getApproval();

    /**
     * Set approval
     *
     * @param string $approval
     * @return $this
     */
    public function setApproval($approval);

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Set price
     * @param float|int $price
     * @return $this
     */
    public function setPrice($price = null);

    /**
     * Get price
     *
     * @return float|int|null
     */
    public function getPrice();

    /**
     * Set attribute_set_id
     *
     * @param int $attribute_set_id
     * @return $this
     */
    public function setAttributeSetId($attribute_set_id);

    /**
     * Get price
     *
     * @return int
     */
    public function getAttributeSetId();

    /**
     * Get product_id
     *
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return $this
     */
    public function setProductId($product_id);

    /**
     * Get is_in_stock
     *
     * @return int
     */
    public function getIsInStock();

    /**
     * Set is_in_stock
     * @param int $is_in_stock
     * @return $this
     */
    public function setIsInStock($is_in_stock = 1);

}
