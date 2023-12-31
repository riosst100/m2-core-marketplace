<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_ChatSystem
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SellerInvoice\Model;

use Lof\SellerInvoice\Api\Data\DataInvoiceInterface;
use Lof\SellerInvoice\Api\Data\SellerinvoiceInterface;

class DataInvoice extends Sellerinvoice implements SellerinvoiceInterface
{
    /**
     * @inheritDoc
     */
    public function getInvoice()
    {
        return $this->getData(self::INVOICE);
    }

    /**
     * @inheritDoc
     */
    public function setInvoice($invoice)
    {
        return $this->setData(self::INVOICE, $invoice);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($product_id)
    {
        return $this->setData(self::PRODUCT_ID, $product_id);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAddress($shippingAddress)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $shippingAddress);
    }

    /**
     * @inheritDoc
     */
    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }

    /**
     * @inheritDoc
     */
    public function setPayment($Payment)
    {
        return $this->setData(self::PAYMENT, $Payment);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * @inheritDoc
     */
    public function getShippingMethod()
    {
        return $this->getData(self::SHIPPING_METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setShippingMethod($shippingMethod)
    {
        return $this->setData(self::SHIPPING_METHOD, $shippingMethod);
    }

    /**
     * @inheritDoc
     */
    public function getPhone()
    {
        return $this->getData(self::CUSTOMER_PHONE);
    }

    /**
     * @inheritDoc
     */
    public function setPhone($phone)
    {
        return $this->setData(self::CUSTOMER_PHONE, $phone);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerName($customer_name)
    {
        return $this->setData(self::CUSTOMER_NAME, $customer_name);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerEmail($customer_email)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customer_email);
    }

    /**
     * @inheritDoc
     */
    public function getSellerName()
    {
        return $this->getData(self::SELLER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setSellerName($seller_name)
    {
        return $this->setData(self::SELLER_NAME, $seller_name);
    }

    /**
     * @inheritDoc
     */
    public function getSellerEmail()
    {
        return $this->getData(self::SELLER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setSellerEmail($seller_email)
    {
        return $this->setData(self::SELLER_EMAIL, $seller_email);
    }
}
