<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SellerInvoice\Api\Data;

interface DataInvoiceInterface
{
    const PRODUCT_ID = 'product_id';
    const SHIPPING_ADDRESS = 'shipping_address';
    const PAYMENT = 'payment';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_PHONE = 'phone';
    const SHIPPING_METHOD = 'shippint_method';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const SELLER_NAME = 'seller_name';
    const SELLER_EMAIL = 'seller_email';
    const INVOICE = 'invoice';

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string product_id
     * @return $this
     */
    public function setProductId($product_id);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getShippingAddress();

    /**
     * Set seller_id
     * @param string $shippingAddress
     * @return $this
     */
    public function setShippingAddress($shippingAddress);

    /**
     * Get payment
     * @return string|null
     */
    public function getPayment();

    /**
     * Set payment
     * @param string $Payment
     * @return $this
     */
    public function setPayment($Payment);

    /**
     * Get payment
     * @return string|null
     */
    public function getPhone();

    /**
     * Set payment
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone);

    /**
     * Get payment
     * @return string|null
     */
    public function getShippingMethod();

    /**
     * Set payment
     * @param string $shippingMethod
     * @return $this
     */
    public function setShippingMethod($shippingMethod);
    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $sellerId
     * @return $this
     */
    public function setSellerId($sellerId);

    /**
     * Get customer_ic
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set $customer_id
     * @param int $customer_id
     * @return $this
     */
    public function setCustomerId($customer_id);
    /**
     * Get seller_order_id
     * @return string|null
     */
    public function getSellerOrderId();

    /**
     * Set seller_order_id
     * @param string $sellerOrderId
     * @return $this
     */
    public function setSellerOrderId($sellerOrderId);

    /**
     * Get invoice_id
     * @return string|null
     */
    public function getInvoiceId();

    /**
     * Set invoice_id
     * @param string $invoiceId
     * @return $this
     */
    public function setInvoiceId($invoiceId);

    /**
     * Get seller_amount
     * @return string|null
     */
    public function getSellerAmount();

    /**
     * Set seller_amount
     * @param string $sellerAmount
     * @return $this
     */
    public function setSellerAmount($sellerAmount);

    /**
     * Get base_grand_total
     * @return string|null
     */
    public function getBaseGrandTotal();

    /**
     * Set base_grand_total
     * @param string $baseGrandTotal
     * @return $this
     */
    public function setBaseGrandTotal($baseGrandTotal);

    /**
     * Get tax_amount
     * @return string|null
     */
    public function getTaxAmount();

    /**
     * Set tax_amount
     * @param string $taxAmount
     * @return $this
     */
    public function setTaxAmount($taxAmount);

    /**
     * Get base_discount_amount
     * @return string|null
     */
    public function getBaseDiscountAmount();

    /**
     * Set base_discount_amount
     * @param string $baseDiscountAmount
     * @return $this
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

    /**
     * Get grand_total
     * @return string|null
     */
    public function getGrandTotal();

    /**
     * Set grand_total
     * @param string $grandTotal
     * @return $this
     */
    public function setGrandTotal($grandTotal);

    /**
     * Get subtotal_incl_tax
     * @return string|null
     */
    public function getSubtotalInclTax();

    /**
     * Set subtotal_incl_tax
     * @param string $subtotalInclTax
     * @return $this
     */
    public function setSubtotalInclTax($subtotalInclTax);

    /**
     * Get total_qty
     * @return string|null
     */
    public function getTotalQty();

    /**
     * Set total_qty
     * @param string $totalQty
     * @return $this
     */
    public function setTotalQty($totalQty);

    /**
     * Get subtotal
     * @return string|null
     */
    public function getSubtotal();

    /**
     * Set subtotal
     * @param string $subtotal
     * @return $this
     */
    public function setSubtotal($subtotal);

    /**
     * Get discount_amount
     * @return string|null
     */
    public function getDiscountAmount();

    /**
     * Set discount_amount
     * @param string $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount);


    /**
     * Get payment
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set payment
     * @param string $customer_name
     * @return $this
     */
    public function setCustomerName($customer_name);

    /**
     * Get payment
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set payment
     * @param string $customer_email
     * @return $this
     */
    public function setCustomerEmail($customer_email);

    /**
     * Get payment
     * @return string|null
     */
    public function getSellerName();

    /**
     * Set payment
     * @param string $seller_name
     * @return $this
     */
    public function setSellerName($seller_name);

    /**
     * Get payment
     * @return string|null
     */
    public function getSellerEmail();

    /**
     * Set payment
     * @param string $seller_email
     * @return $this
     */
    public function setSellerEmail($seller_email);
}

