<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Api\Data;

interface MessageInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const STATUS = 'status';
    const OWNER_ID = 'owner_id';
    const SENDER_ID = 'sender_id';
    const SELLER_SEND = 'seller_send';
    const DESCRIPTION = 'description';
    const SUBJECT = 'subject';
    const RECEIVER_ID = 'receiver_id';
    const CREATED_AT = 'created_at';
    const MESSAGE_ID = 'message_id';
    const SENDER_NAME = 'sender_name';
    const SENDER_EMAIL = 'sender_email';
    const IS_READ = 'is_read';

    /**
     * Get message_id
     * @return int|null
     */
    public function getMessageId();

    /**
     * Set message_id
     * @param int $messageId
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setMessageId($messageId);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
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
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setSubject($subject);

    /**
     * Get sender_email
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * Set sender_email
     * @param string $senderEmail
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
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
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setSenderName($senderName);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
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
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
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
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setIsRead($isRead);

    /**
     * Get sender_id
     * @return string|null
     */
    public function getSenderId();

    /**
     * Set sender_id
     * @param string $senderId
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setSenderId($senderId);

    /**
     * Get owner_id
     * @return int|null
     */
    public function getOwnerId();

    /**
     * Set owner_id
     * @param int $ownerId
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setOwnerId($ownerId);

    /**
     * Get receiver_id
     * @return int|null
     */
    public function getReceiverId();

    /**
     * Set receiver_id
     * @param int $receiverId
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
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
     * @return \Lof\MarketplaceGraphQl\Message\Api\Data\MessageInterface
     */
    public function setSellerSend($sellerSend);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\MarketplaceGraphQl\Api\Data\MessageExtensionInterface|\Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\MarketplaceGraphQl\Api\Data\MessageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\MarketplaceGraphQl\Api\Data\MessageExtensionInterface $extensionAttributes
    );
}

