<?php
/**
 * Copyright © teasd All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\MessageDetailInterface;
use Magento\Framework\Model\AbstractModel;

class MessageDetail extends AbstractModel implements MessageDetailInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\MessageDetail::class);
    }

    /**
     * @inheritDoc
     */
    public function getDetailId()
    {
        return $this->getData(self::DETAIL_ID);
    }

    /**
     * @inheritDoc
     */
    public function setDetailId($detailId)
    {
        return $this->setData(self::DETAIL_ID, $detailId);
    }

    /**
     * @inheritDoc
     */
    public function getMessageId()
    {
        return $this->getData(self::MESSAGE_ID);
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
    public function getSellerSend()
    {
        return $this->getData(self::SELLER_SEND);
    }

    /**
     * @inheritDoc
     */
    public function setSellerSend($sellerSend)
    {
        return $this->setData(self::SELLER_SEND, $sellerSend);
    }

    /**
     * @inheritDoc
     */
    public function getSenderId()
    {
        return $this->getData(self::SENDER_ID);
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
    public function getSenderEmail()
    {
        return $this->getData(self::SENDER_EMAIL);
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
        return $this->getData(self::SENDER_NAME);
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
    public function getReceiverId()
    {
        return $this->getData(self::RECEIVER_ID);
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
    public function getReceiverEmail()
    {
        return $this->getData(self::RECEIVER_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setReceiverEmail($receiverEmail)
    {
        return $this->setData(self::RECEIVER_EMAIL, $receiverEmail);
    }

    /**
     * @inheritDoc
     */
    public function getReceiverName()
    {
        return $this->getData(self::RECEIVER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setReceiverName($receiverName)
    {
        return $this->setData(self::RECEIVER_NAME, $receiverName);
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * @inheritDoc
     */
    public function getIsRead()
    {
        return $this->getData(self::IS_READ);
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
    public function getMessageAdmin()
    {
        return $this->getData(self::MESSAGE_ADMIN);
    }

    /**
     * @inheritDoc
     */
    public function setMessageAdmin($messageAdmin)
    {
        return $this->setData(self::MESSAGE_ADMIN, $messageAdmin);
    }
}

