<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SellerInvoice\Api\Data;

interface SellerinvoiceInterface
{

    const INVOICE_ID = 'invoice_id';
    const CREATED_AT = 'created_at';
    const BASE_SUBTOTAL = 'base_subtotal';
    const SUBTOTAL = 'subtotal';
    const DISCOUNT_DESCRIPTION = 'discount_description';
    const SHIPPING_TAX_AMOUNT = 'shipping_tax_amount';
    const SUBTOTAL_INCL_TAX = 'subtotal_incl_tax';
    const STATE = 'state';
    const SELLER_ID = 'seller_id';
    const BASE_SUBTOTAL_INCL_TAX = 'base_subtotal_incl_tax';
    const BASE_TOTAL_REFUNDED = 'base_total_refunded';
    const BASE_SHIPPING_TAX_AMOUNT = 'base_shipping_tax_amount';
    const SHIPPING_INCL_TAX = 'shipping_incl_tax';
    const TAX_AMOUNT = 'tax_amount';
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const BASE_TAX_AMOUNT = 'base_tax_amount';
    const SELLERINVOICE_ID = 'sellerinvoice_id';
    const BASE_SHIPPING_AMOUNT = 'base_shipping_amount';
    const BASE_SHIPPING_INCL_TAX = 'base_shipping_incl_tax';
    const CUSTOMER_NOTE = 'customer_note';
    const BASE_GRAND_TOTAL = 'base_grand_total';
    const SHIPPING_AMOUNT = 'shipping_amount';
    const SELLER_ORDER_ID = 'seller_order_id';
    const UPDATED_AT = 'updated_at';
    const TOTAL_QTY = 'total_qty';
    const DISCOUNT_AMOUNT = 'discount_amount';
    const GRAND_TOTAL = 'grand_total';
    const SELLER_AMOUNT = 'seller_amount';
    const INVOICE = 'invoice';
    const PRODUCT_ID = 'product_id';
    const SHIPPING_ADDRESS = 'shipping_address';
    const PAYMENT = 'payment';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_PHONE = 'phone';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const SELLER_NAME = 'seller_name';
    const SELLER_EMAIL = 'seller_email';
    const SHIPPING_METHOD = 'shippint_method';

    /**
     * Get sellerinvoice_id
     * @return string|null
     */
    public function getSellerinvoiceId();

    /**
     * Set sellerinvoice_id
     * @param string $sellerinvoiceId
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setSellerinvoiceId($sellerinvoiceId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get seller_order_id
     * @return string|null
     */
    public function getSellerOrderId();

    /**
     * Set seller_order_id
     * @param string $sellerOrderId
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
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
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
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
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
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
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseGrandTotal($baseGrandTotal);

    /**
     * Get shipping_tax_amount
     * @return string|null
     */
    public function getShippingTaxAmount();

    /**
     * Set shipping_tax_amount
     * @param string $shippingTaxAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setShippingTaxAmount($shippingTaxAmount);

    /**
     * Get tax_amount
     * @return string|null
     */
    public function getTaxAmount();

    /**
     * Set tax_amount
     * @param string $taxAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setTaxAmount($taxAmount);

    /**
     * Get base_tax_amount
     * @return string|null
     */
    public function getBaseTaxAmount();

    /**
     * Set base_tax_amount
     * @param string $baseTaxAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseTaxAmount($baseTaxAmount);

    /**
     * Get base_shipping_tax_amount
     * @return string|null
     */
    public function getBaseShippingTaxAmount();

    /**
     * Set base_shipping_tax_amount
     * @param string $baseShippingTaxAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseShippingTaxAmount($baseShippingTaxAmount);

    /**
     * Get base_discount_amount
     * @return string|null
     */
    public function getBaseDiscountAmount();

    /**
     * Set base_discount_amount
     * @param string $baseDiscountAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
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
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setGrandTotal($grandTotal);

    /**
     * Get shipping_amount
     * @return string|null
     */
    public function getShippingAmount();

    /**
     * Set shipping_amount
     * @param string $shippingAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setShippingAmount($shippingAmount);

    /**
     * Get subtotal_incl_tax
     * @return string|null
     */
    public function getSubtotalInclTax();

    /**
     * Set subtotal_incl_tax
     * @param string $subtotalInclTax
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setSubtotalInclTax($subtotalInclTax);

    /**
     * Get base_subtotal_incl_tax
     * @return string|null
     */
    public function getBaseSubtotalInclTax();

    /**
     * Set base_subtotal_incl_tax
     * @param string $baseSubtotalInclTax
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseSubtotalInclTax($baseSubtotalInclTax);

    /**
     * Get base_shipping_amount
     * @return string|null
     */
    public function getBaseShippingAmount();

    /**
     * Set base_shipping_amount
     * @param string $baseShippingAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseShippingAmount($baseShippingAmount);

    /**
     * Get total_qty
     * @return string|null
     */
    public function getTotalQty();

    /**
     * Set total_qty
     * @param string $totalQty
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
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
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setSubtotal($subtotal);

    /**
     * Get base_subtotal
     * @return string|null
     */
    public function getBaseSubtotal();

    /**
     * Set base_subtotal
     * @param string $baseSubtotal
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseSubtotal($baseSubtotal);

    /**
     * Get discount_amount
     * @return string|null
     */
    public function getDiscountAmount();

    /**
     * Set discount_amount
     * @param string $discountAmount
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setDiscountAmount($discountAmount);

    /**
     * Get state
     * @return string|null
     */
    public function getState();

    /**
     * Set state
     * @param string $state
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setState($state);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get shipping_incl_tax
     * @return string|null
     */
    public function getShippingInclTax();

    /**
     * Set shipping_incl_tax
     * @param string $shippingInclTax
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setShippingInclTax($shippingInclTax);

    /**
     * Get base_shipping_incl_tax
     * @return string|null
     */
    public function getBaseShippingInclTax();

    /**
     * Set base_shipping_incl_tax
     * @param string $baseShippingInclTax
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseShippingInclTax($baseShippingInclTax);

    /**
     * Get base_total_refunded
     * @return string|null
     */
    public function getBaseTotalRefunded();

    /**
     * Set base_total_refunded
     * @param string $baseTotalRefunded
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setBaseTotalRefunded($baseTotalRefunded);

    /**
     * Get discount_description
     * @return string|null
     */
    public function getDiscountDescription();

    /**
     * Set discount_description
     * @param string $discountDescription
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setDiscountDescription($discountDescription);

    /**
     * Get customer_note
     * @return string|null
     */
    public function getCustomerNote();

    /**
     * Set customer_note
     * @param string $customerNote
     * @return \Lof\SellerInvoice\Sellerinvoice\Api\Data\SellerinvoiceInterface
     */
    public function setCustomerNote($customerNote);
}

