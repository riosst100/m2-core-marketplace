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

namespace Lof\Quickrfq\Api\Data;

/**
 * Interface QuickrfqInterface
 * @package Lof\Quickrfq\Api\Data
 */
interface QuickrfqInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     *
     */
    const QUANTITY = 'quantity';
    /**
     *
     */
    const PRICE_PER_PRODUCT = 'price_per_product';
    /**
     *
     */
    const CONTACT_NAME = 'contact_name';
    /**
     *
     */
    const QUICKRFQ_ID = 'quickrfq_id';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const EMAIL = 'email';
    /**
     *
     */
    const UPDATE_DATE = 'update_date';
    /**
     *
     */
    const PRODUCT_ID = 'product_id';
    /**
     *
     */
    const PHONE = 'phone';
    /**
     *
     */
    const COMMENT = 'comment';
    /**
     *
     */
    const OVERVIEW = 'overview';
    /**
     *
     */
    const CREATE_DATE = 'create_date';
    /**
     *
     */
    const DATE_NEED_QUOTE = 'date_need_quote';
    /**
     *
     */
    const SELLER_ID = 'seller_id';
    /**
     *
     */
    const SELLER_NAME = 'seller_name';
    /**
     *
     */
    const COUPON_CODE = 'coupon_code';
    /**
     *
     */
    const ADMIN_QUANTITY = 'admin_quantity';
    /**
     *
     */
    const ADMIN_PRICE = 'admin_price';
    /**
     *
     */
    const STORE_ID = 'store_id';
    /**
     *
     */
    const STORE_CURRENCY_CODE = 'store_currency_code';
    /**
     *
     */
    const USER_NAME = 'user_name';
    /**
     *
     */
    const USER_ID = 'user_id';
    /**
     *
     */
    const ATTACHMENT = 'attachment';
    /**
     *
     */
    const ATTRIBUTES = 'attributes';
    /**
     *
     */
    const INFO_BUY_REQUEST = 'info_buy_request';
    /**
     * Available cart id
     */
    const CART_ID = 'cart_id';

    /**
     * Get quickrfq_id
     * @return string|null
     */
    public function getQuickrfqId();

    /**
     * Set quickrfq_id
     * @param string $quickrfqId
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setQuickrfqId($quickrfqId);

    /**
     * @return mixed
     */
    public function getPricePerProduct();


    /**
     * @param $price
     * @return mixed
     */
    public function setPricePerProduct($price);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Quickrfq\Api\Data\QuickrfqExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Quickrfq\Api\Data\QuickrfqExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Quickrfq\Api\Data\QuickrfqExtensionInterface $extensionAttributes
    );

    /**
     * Get contact_name
     * @return string|null
     */
    public function getContactName();

    /**
     * Set contact_name
     * @param string $contactName
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setContactName($contactName);

    /**
     * Get phone
     * @return string|null
     */
    public function getPhone();

    /**
     * Set phone
     * @param string $phone
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setPhone($phone);

    /**
     * Get email
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     * @param string $email
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setEmail($email);

    /**
     * Get comment
     * @return string|null
     */
    public function getComment();

    /**
     * Set comment
     * @param string $comment
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setComment($comment);

    /**
     * Get product_id
     * @return string|null
     */
    public function getProductId();

    /**
     * Set product_id
     * @param string $productId
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setProductId($productId);

    /**
     * Get quantity
     * @return string|null
     */
    public function getQuantity();

    /**
     * Set quantity
     * @param string $quantity
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setQuantity($quantity);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setStatus($status);

    /**
     * Get date_need_quote
     * @return string|null
     */
    public function getDateNeedQuote();

    /**
     * Set date_need_quote
     * @param string $dateNeedQuote
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setDateNeedQuote($dateNeedQuote);

    /**
     * Get create_date
     * @return string|null
     */
    public function getCreateDate();

    /**
     * Set create_date
     * @param string $createDate
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setCreateDate($createDate);

    /**
     * Get update_date
     * @return string|null
     */
    public function getUpdateDate();

    /**
     * Set update_date
     * @param string $updateDate
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setUpdateDate($updateDate);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $seller_id
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setSellerId($seller_id);

    /**
     * Get seller_name
     * @return string|null
     */
    public function getSellerName();

    /**
     * Set seller_name
     * @param string $seller_name
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setSellerName($seller_name);

    /**
     * Get coupon_code
     * @return string|null
     */
    public function getCouponCode();

    /**
     * Set coupon_code
     * @param string $coupon_code
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setCouponCode($coupon_code);

    /**
     * Get admin_quantity
     * @return int|null
     */
    public function getAdminQuantity();

    /**
     * Set admin_quantity
     * @param int $admin_quantity
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setAdminQuantity($admin_quantity);

    /**
     * Get admin_price
     * @return float|int|null
     */
    public function getAdminPrice();

    /**
     * Set admin_price
     * @param float|int $admin_price
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setAdminPrice($admin_price);

    /**
     * Get store_id
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param int $store_id
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setStoreId($store_id);

    /**
     * Get store_currency_code
     * @return string|null
     */
    public function getStoreCurrencyCode();

    /**
     * Set store_currency_code
     * @param string $store_currency_code
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setStoreCurrencyCode($store_currency_code);

    /**
     * Get user_id
     * @return int|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param int $user_id
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setUserId($user_id);

    /**
     * Get user_name
     * @return string|null
     */
    public function getUserName();

    /**
     * Set user_name
     * @param string $user_name
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setUserName($user_name);

    /**
     * Get attributes
     * @return string|null
     */
    public function getAttributes();

    /**
     * Set attributes
     * @param string $attributes
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setAttributes($attributes);

    /**
     * Get info_buy_request
     * @return string|null
     */
    public function getInfoBuyRequest();

    /**
     * Set info_buy_request
     * @param string $info_buy_request
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setInfoBuyRequest($info_buy_request);

    /**
     * Get attachment
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface|null
     */
    public function getAttachment();

    /**
     * Set attachment
     * @param \Lof\Quickrfq\Api\Data\AttachmentInterface|null $attachment
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setAttachment($attachment);

    /**
     * Get cart_id
     * @return int|null
     */
    public function getCartId();

    /**
     * Set cart_id
     * @param int $cart_id
     * @return \Lof\Quickrfq\Api\Data\QuickrfqInterface
     */
    public function setCartId($cart_id);
}
