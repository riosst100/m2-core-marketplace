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

namespace Lof\MarketplaceGraphQl\Model\Data;

use Lof\MarketplaceGraphQl\Api\Data\MessageInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @codeCoverageIgnore
 */
class Message extends \Magento\Framework\Api\AbstractExtensibleObject implements MessageInterface
{
     /**
     * @inheritDoc
     */
    public function getMessageId()
    {
        return $this->_get(self::MESSAGE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMessageId($messageId)
    {
        return $this->setData(self::MESSAGE_ID, $messageId);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @inheritDoc
     */
    public function getSubject()
    {
        return $this->_get(self::SUBJECT);
    }

    /**
     * @inheritDoc
     */
    public function setSubject($subject)
    {
        return $this->setData(self::SUBJECT, $subject);
    }

    /**
     * @inheritDoc
     */
    public function getSenderEmail()
    {
        return $this->_get(self::SENDER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setSenderEmail($senderEmail)
    {
        return $this->setData(self::SENDER_EMAIL, $senderEmail);
    }

    /**
     * @inheritDoc
     */
    public function getSenderName()
    {
        return $this->_get(self::SENDER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setSenderName($senderName)
    {
        return $this->setData(self::SENDER_NAME, $senderName);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
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
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getIsRead()
    {
        return $this->_get(self::IS_READ);
    }

    /**
     * @inheritDoc
     */
    public function setIsRead($isRead)
    {
        return $this->setData(self::IS_READ, $isRead);
    }

    /**
     * @inheritDoc
     */
    public function getSenderId()
    {
        return $this->_get(self::SENDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSenderId($senderId)
    {
        return $this->setData(self::SENDER_ID, $senderId);
    }

    /**
     * @inheritDoc
     */
    public function getOwnerId()
    {
        return $this->_get(self::OWNER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOwnerId($ownerId)
    {
        return $this->setData(self::OWNER_ID, $ownerId);
    }

    /**
     * @inheritDoc
     */
    public function getReceiverId()
    {
        return $this->_get(self::RECEIVER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setReceiverId($receiverId)
    {
        return $this->setData(self::RECEIVER_ID, $receiverId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerSend()
    {
        return $this->_get(self::SELLER_SEND);
    }

    /**
     * @inheritDoc
     */
    public function setSellerSend($sellerSend)
    {
        return $this->setData(self::SELLER_SEND, $sellerSend);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\MarketplaceGraphQl\Api\Data\MessageExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
