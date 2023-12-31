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

use Lof\Quickrfq\Api\Data\AttachmentInterface;

/**
 * Class Attachment
 * @package Lof\Quickrfq\Model\Data
 */
class Attachment extends \Magento\Framework\Api\AbstractExtensibleObject implements AttachmentInterface
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
    public function getMessageId()
    {
        return $this->_get(self::MESSAGE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageId($message_id)
    {
        return $this->setData(self::MESSAGE_ID, $message_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName()
    {
        return $this->_get(self::FILE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileName($file_name)
    {
        return $this->setData(self::FILE_NAME, $file_name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilePath()
    {
        return $this->_get(self::FILE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilePath($file_path)
    {
        return $this->setData(self::FILE_PATH, $file_path);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileType()
    {
        return $this->_get(self::FILE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFileType($file_type)
    {
        return $this->setData(self::FILE_TYPE, $file_type);
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
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Quickrfq\Api\Data\AttachmentExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\Quickrfq\Api\Data\AttachmentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Quickrfq\Api\Data\AttachmentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
