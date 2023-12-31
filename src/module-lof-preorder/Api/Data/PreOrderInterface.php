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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Api\Data;

interface PreOrderInterface
{
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const ITEM_ID = 'item_id';
    const PRODUCT_ID = 'product_id';
    const PARENT_ID = 'parent_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const PREORDER_PERCENT = 'preorder_percent';
    const PAID_AMOUNT = 'paid_amount';
    const REMAINING_AMOUNT = 'remaining_amount';
    const QTY = 'qty';
    const TYPE = 'type';
    const STATUS = 'status';
    const TIME = 'time';
    const CREATED_AT = 'created_at';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $id
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setId($id);
    /**
     * Get order_id
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param int $orderId
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setOrderId($orderId);

    /**
     * Get item_id
     * @return int|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param int $itemId
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setItemId($itemId);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setProductId($product_id);

    /**
     * Get parent_id
     * @return int|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param int $parent_id
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setParentId($parent_id);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     * @param string $customer_email
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setCustomerEmail($customer_email);

    /**
     * Get preorder_percent
     * @return float|null
     */
    public function getPreorderPercent();

    /**
     * Set preorder_percent
     * @param float $preorder_percent
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setPreorderPercent($preorder_percent);

    /**
     * Get paid_amount
     * @return float|null
     */
    public function getPaidAmount();

    /**
     * Set paid_amount
     * @param float $paid_amount
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setPaidAmount($paid_amount);

    /**
     * Get remaining_amount
     * @return float|null
     */
    public function getRemainingAmount();

    /**
     * Set remaining_amount
     * @param float $remaining_amount
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setRemainingAmount($remaining_amount);
    /**
     * Get qty
     * @return int|null
     */
    public function getQty();

    /**
     * Set qty
     * @param int $qty
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setQty($qty);

    /**
     * Set type
     * @param int $type
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setType($type);

    /**
     * Get type
     * @return int|null
     */
    public function getType();

    /**
     * Set status
     * @param int $status
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setStatus($status);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set time
     * @param string $time
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setTime($time);

    /**
     * Get time
     * @return string|null
     */
    public function getTime();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lof\PreOrder\Api\Data\PreOrderInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();
}
