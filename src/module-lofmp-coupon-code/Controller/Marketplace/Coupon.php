<?php
/**
 * LandofCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Controller\Marketplace;

/**
 * Admin seller coupon rule edit controller
 */
class Coupon extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'lofmpcouponcode_coupon';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Lofmp_CouponCode::coupon';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = \Lofmp\CouponCode\Model\Coupon::class;

    /**
     * Collection class name
     * @var string
     */
    protected $_collectionClass      = \Lofmp\CouponCode\Model\ResourceModel\Coupon\Collection::class;

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Lofmp_CouponCode::coupon';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

    /**
     * {@inheritdoc}
     */
    protected $_idKey = 'couponcode_id';
}
