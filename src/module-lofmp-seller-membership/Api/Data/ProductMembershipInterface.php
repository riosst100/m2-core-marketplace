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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Api\Data;

interface ProductMembershipInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const SKU = 'sku';
    const TYPE_ID = 'type_id';
    const NAME = 'name';
    const STATUS = 'status';
    const DURATION = 'seller_duration';
    const DURATION_array = 'duration_array';
    const ATTRIBUTE_SET_ID = 'attribute_set_id';
    const HAS_OPTIONS = 'has_options';
    const REQUIRED_OPTIONS = 'required_options';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PRICE = 'price';
    const TAX_CLASS_ID = 'tax_class_id';
    const FINAL_PRICE = 'final_price';
    const MINIMAL_PRICE = 'minimal_price';
    const MIN_PRICE = 'min_price';
    const MAX_PRICE = 'max_price';
    const TIER_PRICE = 'tier_price';
    const CAT_INDEX_POSITION = 'cat_index_position';
    const URL_KEY = 'url_key';
    const CUSTOMER_GROUP = 'seller_group';
    const FEATURED_PACKAGE = 'seller_featured_package';
    const SHORT_DESCRIPTION = 'short_description';
    const NEWS_FROM_DATE = 'news_from_date';
    const NEWS_TO_DATE = 'news_to_date';
    const STORE_ID = 'store_id';
    const MEMBERSHIP_ORDER = 'seller_membership_order';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entity_id
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setEntityId($entity_id);

    /**
     * Get sku
     * @return string|null
     */
    public function getSku();

    /**
     * Set sku
     * @param string $sku
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setSku($sku);

    /**
     * Get type_id
     * @return string|null
     */
    public function getTypeId();

    /**
     * Set type_id
     * @param string $type_id
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setTypeId($type_id);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setName($name);

    /**
     * Get status
     * @return string|null
     */
    public function getstatus();

    /**
     * Set status
     * @param string $status
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setstatus($status);

    /**
     * Get duration
     * @return string|null
     */
    public function getSellerDuration();

    /**
     * Set duration
     * @param string $duration
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setSellerDuration($duration);

    /**
     * Get duration_array
     * @return string|null
     */
    public function getDurationArray();

    /**
     * Set duration_array
     * @param string $duration_array
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setDurationArray($duration_array);

    /**
     * Get attribute_set_id
     * @return string|null
     */
    public function getAttributeSetId();

    /**
     * Set attribute_set_id
     * @param string $attribute_set_id
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setAttributeSetId($attribute_set_id);

    /**
     * Get has_options
     * @return string|null
     */
    public function getHasOptions();

    /**
     * Set has_options
     * @param string $has_options
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setHasOptions($has_options);

    /**
     * Get required_options
     * @return string|null
     */
    public function getRequiredOptions();

    /**
     * Set required_options
     * @param string $required_options
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setRequiredOptions($required_options);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
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
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setUpdatedAt($updated_at);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set price
     * @param string $price
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setPrice($price);

    /**
     * Get tax_class_id
     * @return string|null
     */
    public function getTaxClassId();

    /**
     * Set tax_class_id
     * @param string $tax_class_id
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setTaxClassId($tax_class_id);

    /**
     * Get final_price
     * @return string|null
     */
    public function getFinalPrice();

    /**
     * Set final_price
     * @param string $final_price
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setFinalPrice($final_price);

    /**
     * Get minimal_price
     * @return string|null
     */
    public function getMinimalPrice();

    /**
     * Set minimal_price
     * @param string $minimal_price
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setMinimalPrice($minimal_price);

    /**
     * Get min_price
     * @return string|null
     */
    public function getMinPrice();

    /**
     * Set min_price
     * @param string $min_price
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setMinPrice($min_price);

    /**
     * Get max_price
     * @return string|null
     */
    public function getMaxPrice();

    /**
     * Set max_price
     * @param string $max_price
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setMaxPrice($max_price);

    /**
     * Get tier_price
     * @return string|null
     */
    public function getTierPrice();

    /**
     * Set tier_price
     * @param string $tier_price
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setTierPrice($tier_price);

    /**
     * Get cat_index_position
     * @return string|null
     */
    public function getCatIndexPosition();

    /**
     * Set cat_index_position
     * @param string $cat_index_position
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setCatIndexPosition($cat_index_position);

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $url_key
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setUrlKey($url_key);

    /**
     * Get customer_group
     * @return string|null
     */
    public function getCustomerGroup();

    /**
     * Set customer_group
     * @param string $customer_group
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setCustomerGroup($customer_group);

    /**
     * Get featured_package
     * @return string|null
     */
    public function getSellerFeaturedPackage();

    /**
     * Set featured_package
     * @param string $featured_package
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setSellerFeaturedPackage($featured_package);

    /**
     * Get short_description
     * @return string|null
     */
    public function getShortDescription();

    /**
     * Set short_description
     * @param string $short_description
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setShortDescription($short_description);

    /**
     * Get news_from_date
     * @return string|null
     */
    public function getNewsFromDate();

    /**
     * Set news_from_date
     * @param string $news_from_date
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setNewsFromDate($news_from_date);

    /**
     * Get news_to_date
     * @return string|null
     */
    public function getNewsToDate();

    /**
     * Set news_to_date
     * @param string $news_to_date
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setNewsToDate($news_to_date);

    /**
     * Get $store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set sku
     * @param string store_id
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setStoreId($store_id);

    /**
     * Get membership_order
     * @return int|null
     */
    public function getSellerMembershipOrder();

    /**
     * Set membership_order
     * @param int|null membership_order
     * @return \Lofmp\SellerMembership\Api\Data\ProductMembershipInterface
     */
    public function setSellerMembershipOrder($membership_order);
}
