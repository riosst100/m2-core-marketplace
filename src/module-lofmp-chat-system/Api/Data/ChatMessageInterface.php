<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Api\Data;

interface ChatMessageInterface
{

    const CUSTOMER_ID = 'customer_id';
    const CHAT_ID = 'chat_id';
    const NAME = 'name';
    const BODY_MSG = 'body_msg';
    const SELLER_ID = 'seller_id';
    const USER_NAME = 'user_name';
    const UPDATED_AT = 'updated_at';
    const CUSTOMER_EMAIL = 'customer_email';
    const CREATED_AT = 'created_at';
    const CUSTOMER_NAME = 'customer_name';
    const MESSAGE_ID = 'message_id';
    const CHATMESSAGE_ID = 'chatmessage_id';
    const USER_ID = 'user_id';
    const IS_READ = 'is_read';

    /**
     * Get chatmessage_id
     * @return string|null
     */
    public function getChatmessageId();

    /**
     * Set chatmessage_id
     * @param string $chatmessageId
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setChatmessageId($chatmessageId);

    /**
     * Get message_id
     * @return string|null
     */
    public function getMessageId();

    /**
     * Set message_id
     * @param string $messageId
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setMessageId($messageId);

    /**
     * Get chat_id
     * @return string|null
     */
    public function getChatId();

    /**
     * Set chat_id
     * @param string $chatId
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setChatId($chatId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get user_id
     * @return string|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param string $userId
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setUserId($userId);

    /**
     * Get customer_id
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param string $customerId
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get customer_name
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set customer_name
     * @param string $customerName
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setCustomerName($customerName);

    /**
     * Get is_read
     * @return string|null
     */
    public function getIsRead();

    /**
     * Set is_read
     * @param string $isRead
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setIsRead($isRead);

    /**
     * Get user_name
     * @return string|null
     */
    public function getUserName();

    /**
     * Set user_name
     * @param string $userName
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setUserName($userName);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setName($name);

    /**
     * Get body_msg
     * @return string|null
     */
    public function getBodyMsg();

    /**
     * Set body_msg
     * @param string $bodyMsg
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setBodyMsg($bodyMsg);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
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
     * @return \Lofmp\ChatSystem\ChatMessage\Api\Data\ChatMessageInterface
     */
    public function setUpdatedAt($updatedAt);
}

