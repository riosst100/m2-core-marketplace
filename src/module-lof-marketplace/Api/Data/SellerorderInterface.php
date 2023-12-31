<?php
/**
 * Copyright © ads All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface SellerorderInterface
{

    const ID = 'id';
    const IS_CANCELED = 'is_canceled';
    const ORDER_ID = 'order_id';
    const CUSTOMER_ID = 'customer_id';
    const STATUS = 'status';
    const COMMISSION = 'commission';
    const SELLER_ID = 'seller_id';
    const IS_SHIPPED = 'is_shipped';
    const IS_REFUNDED = 'is_refunded';
    const SELLER_AMOUNT = 'seller_amount';
    const IS_INVOICED = 'is_invoiced';
    const ORDER_CURRENCY_CODE = 'order_currency_code';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const INCREMENT_ID = 'increment_id';
    const IS_RETURNED = 'is_returned';
    const SHIPPING_AMOUNT = 'shipping_amount';
    const SELLER_SHIPPING_AMOUNT = 'seller_shipping_amount';
    const SELLER_PRODUCT_TOTAL = 'seller_product_total';
    const LINE_ITEMS = 'line_items';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set sellerorder_id
     * @param int $id
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
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
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setOrderId($orderId);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get commission
     * @return float|int|null
     */
    public function getCommission();

    /**
     * Set commission
     * @param float|int|null $commission
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setCommission($commission = null);

    /**
     * Get seller_product_total
     * @return float|int
     */
    public function getSellerProductTotal();

    /**
     * Set seller_product_total
     * @param float|int $sellerProductTotal
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setSellerProductTotal($sellerProductTotal = 0);

    /**
     * Get seller_amount
     * @return float|int
     */
    public function getSellerAmount();

    /**
     * Set seller_amount
     * @param float|int $sellerAmount
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setSellerAmount($sellerAmount);

    /**
     * Get discount_amount
     * @return float|int|null
     */
    public function getDiscountAmount();

    /**
     * Set discount_amount
     * @param float|int|null $discountAmount
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setDiscountAmount($discountAmount = null);

    /**
     * Get is_invoiced
     * @return string|null
     */
    public function getIsInvoiced();

    /**
     * Set is_invoiced
     * @param string $isInvoiced
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setIsInvoiced($isInvoiced);

    /**
     * Get is_shipped
     * @return string|null
     */
    public function getIsShipped();

    /**
     * Set is_shipped
     * @param string $isShipped
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setIsShipped($isShipped);

    /**
     * Get is_refunded
     * @return string|null
     */
    public function getIsRefunded();

    /**
     * Set is_refunded
     * @param string $isRefunded
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setIsRefunded($isRefunded);

    /**
     * Get is_returned
     * @return string|null
     */
    public function getIsReturned();

    /**
     * Set is_returned
     * @param string $isReturned
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setIsReturned($isReturned);

    /**
     * Get is_canceled
     * @return string|null
     */
    public function getIsCanceled();

    /**
     * Set is_canceled
     * @param string $isCanceled
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setIsCanceled($isCanceled);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setStatus($status);

    /**
     * Get increment_id
     * @return string|null
     */
    public function getIncrementId();

    /**
     * Set increment_id
     * @param string $incrementId
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Get shipping_amount
     * @return string|null
     */
    public function getShippingAmount();

    /**
     * Set shipping_amount
     * @param string $shippingAmount
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setShippingAmount($shippingAmount);

    /**
     * Get seller_shipping_amount
     * @return string|null
     */
    public function getSellerShippingAmount();

    /**
     * Set seller_shipping_amount
     * @param string $sellerShippingAmount
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setSellerShippingAmount($sellerShippingAmount);

    /**
     * Get order_currency_code
     * @return string|null
     */
    public function getOrderCurrencyCode();

    /**
     * Set order_currency_code
     * @param string $orderCurrencyCode
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setOrderCurrencyCode($orderCurrencyCode);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get line_items
     * @return \Lof\MarketPlace\Api\Data\SellerorderItemsInterface[]|null
     */
    public function getLineItems();

    /**
     * Set line_items
     * @param \Lof\MarketPlace\Api\Data\SellerorderItemsInterface[]|null $line_items
     * @return \Lof\MarketPlace\Api\Data\SellerorderInterface
     */
    public function setLineItems($line_items = null);

}

