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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Api\Data;

interface AdminMessageInterface
{

    const ADMIN_ID = 'admin_id';
    const OWNER_ID = 'owner_id';
    const STATUS = 'status';
    const SELLER_ID = 'seller_id';
    const SELLER_EMAIL = 'seller_email';
    const SELLER_SEND = 'seller_send';
    const ADMIN_EMAIL = 'admin_email';
    const DESCRIPTION = 'description';
    const MESSAGE_ID = 'message_id';
    const RECEIVER_ID = 'receiver_id';
    const ADMIN_NAME = 'admin_name';
    const CREATED_AT = 'created_at';
    const SELLER_NAME = 'seller_name';
    const SUBJECT = 'subject';
    const IS_READ = 'is_read';

    /**
     * Get message_id
     * @return int|null
     */
    public function getMessageId();

    /**
     * Set message_id
     * @param int $message_id
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setMessageId($message_id);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setDescription($description);

    /**
     * Get subject
     * @return string|null
     */
    public function getSubject();

    /**
     * Set subject
     * @param string $subject
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setSubject($subject);

    /**
     * Get admin_email
     * @return string|null
     */
    public function getAdminEmail();

    /**
     * Set admin_email
     * @param string $adminEmail
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setAdminEmail($adminEmail);

    /**
     * Get admin_name
     * @return string|null
     */
    public function getAdminName();

    /**
     * Set admin_name
     * @param string $adminName
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setAdminName($adminName);

    /**
     * Get seller_email
     * @return string|null
     */
    public function getSellerEmail();

    /**
     * Set seller_email
     * @param string $sellerEmail
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setSellerEmail($sellerEmail);

    /**
     * Get seller_name
     * @return string|null
     */
    public function getSellerName();

    /**
     * Set seller_name
     * @param string $sellerName
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setSellerName($sellerName);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get status
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     * @param int $status
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setStatus($status);

    /**
     * Get is_read
     * @return int|null
     */
    public function getIsRead();

    /**
     * Set is_read
     * @param int $isRead
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setIsRead($isRead);

    /**
     * Get admin_id
     * @return int|null
     */
    public function getAdminId();

    /**
     * Set admin_id
     * @param int $adminId
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setAdminId($adminId);

    /**
     * Get owner_id
     * @return int|null
     */
    public function getOwnerId();

    /**
     * Set owner_id
     * @param int $ownerId
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setOwnerId($ownerId);

    /**
     * Get seller_id
     * @return int|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param int $sellerId
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get receiver_id
     * @return int|null
     */
    public function getReceiverId();

    /**
     * Set receiver_id
     * @param int $receiverId
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setReceiverId($receiverId);

    /**
     * Get seller_send
     * @return int|null
     */
    public function getSellerSend();

    /**
     * Set seller_send
     * @param int $sellerSend
     * @return \Lof\MarketPlace\Api\Data\AdminMessageInterface
     */
    public function setSellerSend($sellerSend);
}

