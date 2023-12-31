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

interface MembershipInterface
{
    const MEMBERSHIP_ID = 'membership_id';
    const GROUP_ID = 'group_id';
    const SELLER_ID = 'seller_id';
    const NAME = 'name';
    const DURATION = 'duration';
    const PRICE = 'price';
    const EXPIRATION_DATE= 'expiration_date';
    const CREATED_AT = 'created_at';
    const STATUS = 'status';
    const PRODUCT_ID = 'product_id';
    const PRODUCT_OPTIONS = 'product_options';
    const ITEM_ID = 'item_id';
    const BEFORE_SELLER_GROUP_ID = 'before_seller_group_id';

    /**
     * Get membership_id
     * @return int|null
     */
    public function getMembershipId();

    /**
     * Set membership_id
     * @param int $membership_id
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setMembershipId($membership_id);

    /**
     * Get group_id
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param int $group_id
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setGroupId($group_id);

    /**
     * Get before_seller_group_id
     * @return int|null
     */
    public function getBeforeSellerGroupId();

    /**
     * Set before_seller_group_id
     * @param int $before_seller_group_id
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setBeforeSellerGroupId($before_seller_group_id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setSellerId($seller_id);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setName($name);

    /**
     * Get duration
     * @return string|null
     */
    public function getDuration();

    /**
     * Set duration
     * @param string $duration
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setDuration($duration);

    /**
     * Get price
     * @return float|null
     */
    public function getPrice();

    /**
     * Set price
     * @param float $price
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setPrice($price);

    /**
     * Get expiration_date
     * @return string|null
     */
    public function getExpirationDate();

    /**
     * Set expiration_date
     * @param string $expiration_date
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setExpirationDate($expiration_date);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setStatus($status);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setProductId($product_id);

    /**
     * Get product_options
     * @return string|null
     */
    public function getProductOptions();

    /**
     * Set product_options
     * @param string $product_options
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setProductOptions($product_options);

    /**
     * Get item_id
     * @return int|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param int $item_id
     * @return \Lofmp\SellerMembership\Api\Data\MembershipInterface
     */
    public function setItemId($item_id);
}
