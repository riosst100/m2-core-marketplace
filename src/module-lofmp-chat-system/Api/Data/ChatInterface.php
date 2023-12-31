<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\ChatSystem\Api\Data;

interface ChatInterface
{

    const BROWSER = 'browser';
    const USER_NAME = 'user_name';
    const CREATED_AT = 'created_at';
    const CUSTOMER_NAME = 'customer_name';
    const SESSION_ID = 'session_id';
    const USER_ID = 'user_id';
    const CHAT_ID = 'chat_id';
    const SELLER_ID = 'seller_id';
    const CUSTOMER_ID  = 'customer_id';
    const COUNTRY = 'country';
    const IP = 'ip';
    const OS = 'os';
    const PHONE_NUMBER = 'phone_number';
    const NUMBER_MESSAGE = 'number_message';
    const IS_READ = 'is_read';
    const STATUS = 'status';
    const UPDATED_AT = 'updated_at';
    const USER_AGENT = 'user_agent';
    const CUSTOMER_EMAIL = 'customer_email';
    const CURRENT_URL = 'current_url';
    const MESSAGES = 'messages';

    /**
     * Get chat_id
     * @return int|null
     */
    public function getChatId();

    /**
     * Set chat_id
     * @param int $chatId
     * @return $this
     */
    public function setChatId($chatId);

    /**
     * Get user_id
     * @return int|null
     */
    public function getUserId();

    /**
     * Set user_id
     * @param int $userId
     * @return $this
     */
    public function setUserId($userId);

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
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customerId
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setCustomerName($customerName);

    /**
     * Get user_name
     * @return string|null
     */
    public function getUserName();

    /**
     * Set user_name
     * @param string $userName
     * @return $this
     */
    public function setUserName($userName);

    /**
     * Get is_read
     * @return string|null
     */
    public function getIsRead();

    /**
     * Set is_read
     * @param string $isRead
     * @return $this
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
     * @return $this
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
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get ip
     * @return string|null
     */
    public function getIp();

    /**
     * Set ip
     * @param string $ip
     * @return $this
     */
    public function setIp($ip);

    /**
     * Get number_message
     * @return string|null
     */
    public function getNumberMessage();

    /**
     * Set number_message
     * @param string $numberMessage
     * @return $this
     */
    public function setNumberMessage($numberMessage);

    /**
     * Get user_agent
     * @return string|null
     */
    public function getUserAgent();

    /**
     * Set user_agent
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent);

    /**
     * Get browser
     * @return string|null
     */
    public function getBrowser();

    /**
     * Set browser
     * @param string $browser
     * @return $this
     */
    public function setBrowser($browser);

    /**
     * Get os
     * @return string|null
     */
    public function getOs();

    /**
     * Set os
     * @param string $os
     * @return $this
     */
    public function setOs($os);

    /**
     * Get country
     * @return string|null
     */
    public function getCountry();

    /**
     * Set country
     * @param string $country
     * @return $this
     */
    public function setCountry($country);

    /**
     * Get phone_number
     * @return string|null
     */
    public function getPhoneNumber();

    /**
     * Set phone_number
     * @param string $phoneNumber
     * @return $this
     */
    public function setPhoneNumber($phoneNumber);

    /**
     * Get current_url
     * @return string|null
     */
    public function getCurrentUrl();

    /**
     * Set current_url
     * @param string $currentUrl
     * @return $this
     */
    public function setCurrentUrl($currentUrl);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get session_id
     * @return string|null
     */
    public function getSessionId();

    /**
     * Set session_id
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId($sessionId);
}

