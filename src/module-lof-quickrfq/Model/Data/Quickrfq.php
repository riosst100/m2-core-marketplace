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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lof\Quickrfq\Model\Data;

use Lof\Quickrfq\Api\Data\QuickrfqInterface;

/**
 * Class Quickrfq
 * @package Lof\Quickrfq\Model\Data
 */
class Quickrfq extends \Magento\Framework\Api\AbstractExtensibleObject implements QuickrfqInterface
{

    /**
     * Get quickrfq_id
     * @return string|null
     */
    public function getQuickrfqId()
    {
        return $this->_get(self::QUICKRFQ_ID);
    }

    /**
     * Set quickrfq_id
     * @param string $quickrfqId
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setQuickrfqId($quickrfqId)
    {
        return $this->setData(self::QUICKRFQ_ID, $quickrfqId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Quickrfq\Api\Data\QuickrfqExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\Quickrfq\Api\Data\QuickrfqExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Quickrfq\Api\Data\QuickrfqExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get contact_name
     * @return string|null
     */
    public function getContactName()
    {
        return $this->_get(self::CONTACT_NAME);
    }

    /**
     * Set contact_name
     * @param string $contactName
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setContactName($contactName)
    {
        return $this->setData(self::CONTACT_NAME, $contactName);
    }

    /**
     * Get phone
     * @return string|null
     */
    public function getPhone()
    {
        return $this->_get(self::PHONE);
    }

    /**
     * Set phone
     * @param string $phone
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setPhone($phone)
    {
        return $this->setData(self::PHONE, $phone);
    }

    /**
     * Get email
     * @return string|null
     */
    public function getEmail()
    {
        return $this->_get(self::EMAIL);
    }

    /**
     * Set email
     * @param string $email
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * Get comment
     * @return string|null
     */
    public function getComment()
    {
        return $this->_get(self::COMMENT);
    }

    /**
     * Set comment
     * @param string $comment
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * Get price per product
     * @return mixed|null
     */
    public function getPricePerProduct()
    {
        return $this->_get(self::PRICE_PER_PRODUCT);
    }

    /**
     * Set price per product
     * @param string $price
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setPricePerProduct($price)
    {
        return $this->setData(self::PRICE_PER_PRODUCT, $price);
    }

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set product_id
     * @param string $productId
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get quantity
     * @return string|null
     */
    public function getQuantity()
    {
        return $this->_get(self::QUANTITY);
    }

    /**
     * Set quantity
     * @param string $quantity
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setQuantity($quantity)
    {
        return $this->setData(self::QUANTITY, $quantity);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get date_need_quote
     * @return string|null
     */
    public function getDateNeedQuote()
    {
        return $this->_get(self::DATE_NEED_QUOTE);
    }

    /**
     * Set date_need_quote
     * @param string $dateNeedQuote
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setDateNeedQuote($dateNeedQuote)
    {
        return $this->setData(self::DATE_NEED_QUOTE, $dateNeedQuote);
    }

    /**
     * Get create_date
     * @return string|null
     */
    public function getCreateDate()
    {
        return $this->_get(self::CREATE_DATE);
    }

    /**
     * Set create_date
     * @param string $createDate
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setCreateDate($createDate)
    {
        return $this->setData(self::CREATE_DATE, $createDate);
    }

    /**
     * Get update_date
     * @return string|null
     */
    public function getUpdateDate()
    {
        return $this->_get(self::UPDATE_DATE);
    }

    /**
     * Set update_date
     * @param string $updateDate
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setUpdateDate($updateDate)
    {
        return $this->setData(self::UPDATE_DATE, $updateDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerId()
    {
        return $this->_get(self::SELLER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerName()
    {
        return $this->_get(self::SELLER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerName($seller_name)
    {
        return $this->setData(self::SELLER_NAME, $seller_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponCode()
    {
        return $this->_get(self::COUPON_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponCode($coupon_code)
    {
        return $this->setData(self::COUPON_CODE, $coupon_code);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminQuantity()
    {
        return $this->_get(self::ADMIN_QUANTITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminQuantity($admin_quantity)
    {
        return $this->setData(self::ADMIN_QUANTITY, $admin_quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminPrice()
    {
        return $this->_get(self::ADMIN_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdminPrice($admin_price)
    {
        return $this->setData(self::ADMIN_PRICE, $admin_price);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($store_id)
    {
        return $this->setData(self::STORE_ID, $store_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreCurrencyCode()
    {
        return $this->_get(self::STORE_CURRENCY_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreCurrencyCode($store_currency_code)
    {
        return $this->setData(self::STORE_CURRENCY_CODE, $store_currency_code);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->_get(self::USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($user_id)
    {
        return $this->setData(self::USER_ID, $user_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserName()
    {
        return $this->_get(self::USER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserName($user_name)
    {
        return $this->setData(self::USER_NAME, $user_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->_get(self::ATTRIBUTES);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes($attributes)
    {
        return $this->setData(self::ATTRIBUTES, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoBuyRequest()
    {
        return $this->_get(self::INFO_BUY_REQUEST);
    }

    /**
     * {@inheritdoc}
     */
    public function setInfoBuyRequest($info_buy_request)
    {
        return $this->setData(self::INFO_BUY_REQUEST, $info_buy_request);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachment()
    {
        return $this->_get(self::ATTACHMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttachment($attachment)
    {
        return $this->setData(self::ATTACHMENT, $attachment);
    }

    /**
     * {@inheritdoc}
     */
    public function getCartId()
    {
        return $this->_get(self::CART_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartId($cart_id)
    {
        return $this->setData(self::CART_ID, $cart_id);
    }
}
