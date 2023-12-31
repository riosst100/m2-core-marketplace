<?php
/**
 * Lof CouponCode is a powerful tool for managing the processing return and exchange requests within your workflow. This, in turn, allows your customers to request and manage returns and exchanges directly from your webstore. The Extension compatible with magento 2.x
 * Copyright (C) 2017  Landofcoder.com
 *
 * This file is part of Lofmp/CouponCode.
 *
 * Lofmp/CouponCode is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Lofmp\CouponCode\Api\Data;

interface CouponInterface
{

    const COUPONCODE_ID = 'couponcode_id';
    const COUPON_ID = 'coupon_id';
    const ALIAS = 'alias';
    const CODE = 'code';
    const RULE_ID = 'rule_id';
    const CUSTOMER_ID = 'customer_id';
    const SELLER_ID = 'seller_id';
    const EMAIL = 'email';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const USES_PER_CUSTOMER = 'uses_per_customer';
    const IS_ACTIVE = 'is_active';
    const SIMPLE_ACTION = 'simple_action';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const DISCOUNT_QTY = 'discount_qty';
    const TIMES_USED = 'times_used';
    const USES_PER_COUPON = 'uses_per_coupon';
    const USAGE_LIMIT = 'usage_limit';
    const USAGE_PER_CUSTOMER = 'usage_per_customer';
    const EXPIRATION_DATE = 'expiration_date';
    const CREATED_AT = 'created_at';
    const TYPE = 'type';
    const IS_PUBLIC = 'is_public';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const ACTIONS_SERIALIZED = 'actions_serialized';

    /**
     * Get coupon_id
     * @return int|null
     */
    public function getCouponId();

    /**
     * Set coupon_id
     * @param int $coupon_id
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setCouponId($couponId);

    /**
     * Get couponcode_id
     * @return int|null
     */
    public function getCouponcodeId();

    /**
     * Set couponcode_id
     * @param int $couponcode_id
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setCouponcodeId($couponcode_id);

    /**
     * Get alias
     * @return string|null
     */
    public function getAlias();

    /**
     * Set alias
     * @param string $alias
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setAlias($alias);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setDescription($description);

    /**
     * Get from_date
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set from_date
     * @param string $from_date
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setFromDate($from_date);

    /**
     * Get to_date
     * @return string|null
     */
    public function getToDate();

    /**
     * Set to_date
     * @param string $to_date
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setToDate($to_date);

    /**
     * Get times_used
     * @return int|null
     */
    public function getTimesUsed();

    /**
     * Set times_used
     * @param int $times_used
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setTimesUsed($times_used);

    /**
     * Get discount_amount
     * @return float|int
     */
    public function getDiscountAmount();

    /**
     * Set discount_amount
     * @param float|int $times_discount_amount
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setDiscountAmount($discount_amount);

    /**
     * Get simple_action
     * @return string|null
     */
    public function getSimpleAction();

    /**
     * Set simple_action
     * @param string $simple_action
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setSimpleAction($simple_action);

    /**
     * Get code
     * @return string|null
     */
    public function getCode();

    /**
     * Set code
     * @param string $code
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setCode($code);

    /**
     * Get rule_id
     * @return int|null
     */
    public function getRuleId();

    /**
     * Set rule_id
     * @param int $rule_id
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setRuleId($rule_id);

    /**
     * Get usage_limit
     * @return int|null
     */
    public function getUsageLimit();

    /**
     * Set usage_limit
     * @param int|null $usage_limit
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setUsageLimit($usage_limit = null);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setSellerId($seller_id);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setEmail($email);

    /**
     * Get is_public
     * @return int|null
     */
    public function getIsPublic();

    /**
     * Set is_public
     * @param int $is_public
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setIsPublic($is_public);

    /**
     * Get actions_serialized
     * @return string|null
     */
    public function getActionsSerialized();

    /**
     * Set actions_serialized
     * @param string $actions_serialized
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setActionsSerialized($actions_serialized);

    /**
     * Get conditions_serialized
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Set conditions_serialized
     * @param string $conditions_serialized
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setConditionsSerialized($conditions_serialized);


    /**
     * Get magento sales rule of coupon
     * @return \Magento\SalesRule\Api\Data\RuleInterface|null
     */
    public function getSalesRuleCoupon();
}
