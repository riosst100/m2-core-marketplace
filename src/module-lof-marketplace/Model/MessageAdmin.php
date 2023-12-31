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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\AdminMessageInterface;

class MessageAdmin extends \Magento\Framework\Model\AbstractModel implements AdminMessageInterface
{
    const STATUS_DRAFT = 0;
    const STATUS_UNREAD = 1;
    const STATUS_READ = 2;
    const STATUS_SENT = 3;

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\MessageAdmin::class);
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
    public function setMessageId($message_id)
    {
        return $this->setData(self::MESSAGE_ID, $message_id);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
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
        return $this->getData(self::SUBJECT);
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
    public function getAdminEmail()
    {
        return $this->getData(self::ADMIN_EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setAdminEmail($adminEmail)
    {
        return $this->setData(self::ADMIN_EMAIL, $adminEmail);
    }

    /**
     * @inheritDoc
     */
    public function getAdminName()
    {
        return $this->getData(self::ADMIN_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setAdminName($adminName)
    {
        return $this->setData(self::ADMIN_NAME, $adminName);
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
    public function setSellerEmail($sellerEmail)
    {
        return $this->setData(self::SELLER_EMAIL, $sellerEmail);
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
    public function setSellerName($sellerName)
    {
        return $this->setData(self::SELLER_NAME, $sellerName);
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
    public function getStatus()
    {
        return $this->getData(self::STATUS);
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
    public function getAdminId()
    {
        return $this->getData(self::ADMIN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setAdminId($adminId)
    {
        return $this->setData(self::ADMIN_ID, $adminId);
    }

    /**
     * @inheritDoc
     */
    public function getOwnerId()
    {
        return $this->getData(self::OWNER_ID);
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
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
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
}
