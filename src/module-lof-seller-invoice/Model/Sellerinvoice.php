<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SellerInvoice\Model;

use Lof\SellerInvoice\Api\Data\SellerinvoiceInterface;
use Magento\Framework\Model\AbstractModel;

class Sellerinvoice extends AbstractModel implements SellerinvoiceInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\SellerInvoice\Model\ResourceModel\Sellerinvoice::class);
    }

    /**
     * @inheritDoc
     */
    public function getSellerinvoiceId()
    {
        return $this->getData(self::SELLERINVOICE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerinvoiceId($sellerinvoiceId)
    {
        return $this->setData(self::SELLERINVOICE_ID, $sellerinvoiceId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerOrderId()
    {
        return $this->getData(self::SELLER_ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerOrderId($sellerOrderId)
    {
        return $this->setData(self::SELLER_ORDER_ID, $sellerOrderId);
    }

    /**
     * @inheritDoc
     */
    public function getInvoiceId()
    {
        return $this->getData(self::INVOICE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setInvoiceId($invoiceId)
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerAmount()
    {
        return $this->getData(self::SELLER_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setSellerAmount($sellerAmount)
    {
        return $this->setData(self::SELLER_AMOUNT, $sellerAmount);
    }

    /**
     * @inheritDoc
     */
    public function getBaseGrandTotal()
    {
        return $this->getData(self::BASE_GRAND_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setBaseGrandTotal($baseGrandTotal)
    {
        return $this->setData(self::BASE_GRAND_TOTAL, $baseGrandTotal);
    }

    /**
     * @inheritDoc
     */
    public function getShippingTaxAmount()
    {
        return $this->getData(self::SHIPPING_TAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setShippingTaxAmount($shippingTaxAmount)
    {
        return $this->setData(self::SHIPPING_TAX_AMOUNT, $shippingTaxAmount);
    }

    /**
     * @inheritDoc
     */
    public function getTaxAmount()
    {
        return $this->getData(self::TAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setTaxAmount($taxAmount)
    {
        return $this->setData(self::TAX_AMOUNT, $taxAmount);
    }

    /**
     * @inheritDoc
     */
    public function getBaseTaxAmount()
    {
        return $this->getData(self::BASE_TAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseTaxAmount($baseTaxAmount)
    {
        return $this->setData(self::BASE_TAX_AMOUNT, $baseTaxAmount);
    }

    /**
     * @inheritDoc
     */
    public function getBaseShippingTaxAmount()
    {
        return $this->getData(self::BASE_SHIPPING_TAX_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseShippingTaxAmount($baseShippingTaxAmount)
    {
        return $this->setData(self::BASE_SHIPPING_TAX_AMOUNT, $baseShippingTaxAmount);
    }

    /**
     * @inheritDoc
     */
    public function getBaseDiscountAmount()
    {
        return $this->getData(self::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseDiscountAmount($baseDiscountAmount)
    {
        return $this->setData(self::BASE_DISCOUNT_AMOUNT, $baseDiscountAmount);
    }

    /**
     * @inheritDoc
     */
    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->setData(self::GRAND_TOTAL, $grandTotal);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAmount($shippingAmount)
    {
        return $this->setData(self::SHIPPING_AMOUNT, $shippingAmount);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotalInclTax()
    {
        return $this->getData(self::SUBTOTAL_INCL_TAX);
    }

    /**
     * @inheritDoc
     */
    public function setSubtotalInclTax($subtotalInclTax)
    {
        return $this->setData(self::SUBTOTAL_INCL_TAX, $subtotalInclTax);
    }

    /**
     * @inheritDoc
     */
    public function getBaseSubtotalInclTax()
    {
        return $this->getData(self::BASE_SUBTOTAL_INCL_TAX);
    }

    /**
     * @inheritDoc
     */
    public function setBaseSubtotalInclTax($baseSubtotalInclTax)
    {
        return $this->setData(self::BASE_SUBTOTAL_INCL_TAX, $baseSubtotalInclTax);
    }

    /**
     * @inheritDoc
     */
    public function getBaseShippingAmount()
    {
        return $this->getData(self::BASE_SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setBaseShippingAmount($baseShippingAmount)
    {
        return $this->setData(self::BASE_SHIPPING_AMOUNT, $baseShippingAmount);
    }

    /**
     * @inheritDoc
     */
    public function getTotalQty()
    {
        return $this->getData(self::TOTAL_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setTotalQty($totalQty)
    {
        return $this->setData(self::TOTAL_QTY, $totalQty);
    }

    /**
     * @inheritDoc
     */
    public function getSubtotal()
    {
        return $this->getData(self::SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setSubtotal($subtotal)
    {
        return $this->setData(self::SUBTOTAL, $subtotal);
    }

    /**
     * @inheritDoc
     */
    public function getBaseSubtotal()
    {
        return $this->getData(self::BASE_SUBTOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setBaseSubtotal($baseSubtotal)
    {
        return $this->setData(self::BASE_SUBTOTAL, $baseSubtotal);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountAmount($discountAmount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    /**
     * @inheritDoc
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * @inheritDoc
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * @inheritDoc
     */
    public function getShippingInclTax()
    {
        return $this->getData(self::SHIPPING_INCL_TAX);
    }

    /**
     * @inheritDoc
     */
    public function setShippingInclTax($shippingInclTax)
    {
        return $this->setData(self::SHIPPING_INCL_TAX, $shippingInclTax);
    }

    /**
     * @inheritDoc
     */
    public function getBaseShippingInclTax()
    {
        return $this->getData(self::BASE_SHIPPING_INCL_TAX);
    }

    /**
     * @inheritDoc
     */
    public function setBaseShippingInclTax($baseShippingInclTax)
    {
        return $this->setData(self::BASE_SHIPPING_INCL_TAX, $baseShippingInclTax);
    }

    /**
     * @inheritDoc
     */
    public function getBaseTotalRefunded()
    {
        return $this->getData(self::BASE_TOTAL_REFUNDED);
    }

    /**
     * @inheritDoc
     */
    public function setBaseTotalRefunded($baseTotalRefunded)
    {
        return $this->setData(self::BASE_TOTAL_REFUNDED, $baseTotalRefunded);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountDescription()
    {
        return $this->getData(self::DISCOUNT_DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountDescription($discountDescription)
    {
        return $this->setData(self::DISCOUNT_DESCRIPTION, $discountDescription);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerNote()
    {
        return $this->getData(self::CUSTOMER_NOTE);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerNote($customerNote)
    {
        return $this->setData(self::CUSTOMER_NOTE, $customerNote);
    }
}

