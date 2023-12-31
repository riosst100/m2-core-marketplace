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

interface CompleteItemInterface
{
    const ORDER_ITEM_ID = 'order_item_id';
    const QUOTE_ITEM_ID = 'quote_item_id';
    const ORDER_ID = 'order_id';
    const CUSTOMER_ID = 'customer_id';
    const PRODUCT_ID = 'product_id';
    const QTY = 'qty';
    /**
     * Get order_item_id
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * Set order_item_id
     * @param int $order_item_id
     * @return \Lof\PreOrder\Api\Data\CompleteItemInterface
     */
    public function setOrderItemId($order_item_id);

    /**
     * Get quote_item_id
     * @return int|null
     */
    public function getQuoteItemId();

    /**
     * Set quote_item_id
     * @param int $quote_item_id
     * @return \Lof\PreOrder\Api\Data\CompleteItemInterface
     */
    public function setQuoteItemId($quote_item_id);

    /**
     * Get order_id
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order_id
     * @param int $order_id
     * @return \Lof\PreOrder\Api\Data\CompleteItemInterface
     */
    public function setOrderId($order_id);

    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return \Lof\PreOrder\Api\Data\CompleteItemInterface
     */
    public function setCustomerId($customer_id);

    /**
     * Get product_id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param int $product_id
     * @return \Lof\PreOrder\Api\Data\CompleteItemInterface
     */
    public function setProductId($product_id);

    /**
     * Get qty
     * @return int|null
     */
    public function getQty();

    /**
     * Set qty
     * @param int $qty
     * @return \Lof\PreOrder\Api\Data\CompleteItemInterface
     */
    public function setQty($qty);
}
