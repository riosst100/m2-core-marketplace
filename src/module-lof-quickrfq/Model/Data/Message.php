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

use Lof\Quickrfq\Api\Data\MessageInterface;

/**
 * Class Message
 * @package Lof\Quickrfq\Model\Data
 */
class Message extends \Magento\Framework\Api\AbstractExtensibleObject implements MessageInterface
{

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuickrfqId()
    {
        return $this->_get(self::QUICKRFQ_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuickrfqId($quickrfq_id)
    {
        return $this->setData(self::QUICKRFQ_ID, $quickrfq_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsMain()
    {
        return $this->_get(self::IS_MAIN);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsMain($is_main)
    {
        return $this->setData(self::IS_MAIN, $is_main);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
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
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Quickrfq\Api\Data\MessageExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\Quickrfq\Api\Data\MessageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Quickrfq\Api\Data\MessageExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}