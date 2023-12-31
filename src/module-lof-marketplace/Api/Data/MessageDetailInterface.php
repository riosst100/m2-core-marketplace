<?php
/**
 * Copyright © teasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Api\Data;

interface MessageDetailInterface
{

    const DETAIL_ID = 'detail_id';
    const RECEIVER_NAME = 'receiver_name';
    const SENDER_ID = 'sender_id';
    const SELLER_SEND = 'seller_send';
    const MESSAGE_ADMIN = 'message_admin';
    const CONTENT = 'content';
    const RECEIVER_ID = 'receiver_id';
    const CREATED_AT = 'created_at';
    const RECEIVER_EMAIL = 'receiver_email';
    const MESSAGE_ID = 'message_id';
    const SENDER_NAME = 'sender_name';
    const SENDER_EMAIL = 'sender_email';
    const IS_READ = 'is_read';

    /**
     * Get detail_id
     * @return int|null
     */
    public function getDetailId();

    /**
     * Set detail_id
     * @param int $detailId
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setDetailId($detailId);

    /**
     * Get message_id
     * @return int|null
     */
    public function getMessageId();

    /**
     * Set message_id
     * @param int $messageId
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setMessageId($messageId);

    /**
     * Get seller_send
     * @return string|null
     */
    public function getSellerSend();

    /**
     * Set seller_send
     * @param string $sellerSend
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setSellerSend($sellerSend);

    /**
     * Get sender_id
     * @return int|null
     */
    public function getSenderId();

    /**
     * Set sender_id
     * @param int $senderId
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setSenderId($senderId);

    /**
     * Get sender_email
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * Set sender_email
     * @param string $senderEmail
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setSenderEmail($senderEmail);

    /**
     * Get sender_name
     * @return string|null
     */
    public function getSenderName();

    /**
     * Set sender_name
     * @param string $senderName
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setSenderName($senderName);

    /**
     * Get receiver_id
     * @return int|null
     */
    public function getReceiverId();

    /**
     * Set receiver_id
     * @param int $receiverId
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setReceiverId($receiverId);

    /**
     * Get receiver_email
     * @return string|null
     */
    public function getReceiverEmail();

    /**
     * Set receiver_email
     * @param string $receiverEmail
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setReceiverEmail($receiverEmail);

    /**
     * Get receiver_name
     * @return string|null
     */
    public function getReceiverName();

    /**
     * Set receiver_name
     * @param string $receiverName
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setReceiverName($receiverName);

    /**
     * Get content
     * @return string|null
     */
    public function getContent();

    /**
     * Set content
     * @param string $content
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setContent($content);

    /**
     * Get is_read
     * @return int|null
     */
    public function getIsRead();

    /**
     * Set is_read
     * @param int $isRead
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setIsRead($isRead);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get message_admin
     * @return int|null
     */
    public function getMessageAdmin();

    /**
     * Set message_admin
     * @param int $messageAdmin
     * @return \Lof\MarketPlace\Api\Data\MessageDetailInterface
     */
    public function setMessageAdmin($messageAdmin);
}

