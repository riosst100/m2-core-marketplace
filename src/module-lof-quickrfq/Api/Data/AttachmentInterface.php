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

namespace Lof\Quickrfq\Api\Data;

/**
 * Interface AttachmentInterface
 * @package Lof\Quickrfq\Api\Data
 */
interface AttachmentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const ENTITY_ID = 'entity_id';
    /**
     *
     */
    const QUICKRFQ_ID = 'quickrfq_id';
    /**
     *
     */
    const MESSAGE_ID = 'message_id';
    /**
     *
     */
    const FILE_NAME = 'file_name';
    /**
     *
     */
    const FILE_PATH = 'file_path';
    /**
     *
     */
    const FILE_TYPE = 'file_type';
    /**
     *
     */
    const CREATED_AT = 'created_at';
    /**
     * Get entity_id
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param int $entity_id
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface
     */
    public function setEntityId($entity_id);
    /**
     * Get quickrfq_id
     * @return int|null
     */
    public function getQuickrfqId();

    /**
     * Set quickrfq_id
     * @param int $quickrfq_id
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface
     */
    public function setQuickrfqId($quickrfq_id);
    /**
     * Get message_id
     * @return int|null
     */
    public function getMessageId();

    /**
     * Set message_id
     * @param int $message_id
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface
     */
    public function setMessageId($message_id);
    /**
     * Get file_path
     * @return string|null
     */
    public function getFilePath();

    /**
     * Set file_path
     * @param string $file_path
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface
     */
    public function setFilePath($file_path);
    /**
     * Get file_type
     * @return string|null
     */
    public function getFileType();

    /**
     * Set file_type
     * @param string $file_type
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface
     */
    public function setFileType($file_type);
    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Quickrfq\Api\Data\AttachmentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Quickrfq\Api\Data\AttachmentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Quickrfq\Api\Data\AttachmentExtensionInterface $extensionAttributes
    );
}
