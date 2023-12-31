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

interface CancelrequestInterface
{

    const ENTITY_ID = 'entity_id';
    const MEMBERSHIP_ID = 'membership_id';
    const STATUS = 'status';
    const CUSTOMER_COMMENT = 'customer_comment';
    const ADMIN_COMMENT = 'admin_comment';
    const CREATION_TIME = 'creation_time';
    const NAME = 'name';
    const DURATION= 'duration';
    const PRICE= 'price';
    const PRODUCT_ID= 'product_id';

    /**
     * Get entity_id
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param int $entity_id
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setEntityId($entity_id);

    /**
     * Get membership_id
     * @return int|null
     */
    public function getMembershipId();

    /**
     * Set membership_id
     * @param int $membership_id
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setMembershipId($membership_id);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setStatus($status);

    /**
     * Get customer_comment
     * @return string|null
     */
    public function getCustomerComment();

    /**
     * Set customer_comment
     * @param string $customer_comment
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setCustomerComment($customer_comment);

    /**
     * Get admin_comment
     * @return string|null
     */
    public function getAdminComment();

    /**
     * Set admin_comment
     * @param string $admin_comment
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setAdminComment($admin_comment);

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set creation_time
     * @param string $creation_time
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setCreationTime($creation_time);

    /**
     * Set name
     * @param string $name
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setName($name);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set duration
     * @param string $duration
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setDuration($duration);

    /**
     * Get duration
     * @return string|null
     */
    public function getDuration();

    /**
     * Set price
     * @param string $price
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setPrice($price);

    /**
     * Get price
     * @return string|null
     */
    public function getPrice();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lofmp\SellerMembership\Api\Data\CancelrequestInterface
     */
    public function setProductId($product_id);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();
}
