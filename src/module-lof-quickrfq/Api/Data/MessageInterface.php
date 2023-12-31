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
 * Interface MessageInterface
 * @package Lof\Quickrfq\Api\Data
 */
interface MessageInterface extends \Magento\Framework\Api\ExtensibleDataInterface
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
    const CUSTOMER_ID = 'customer_id';
    /**
     *
     */
    const IS_MAIN = 'is_main';
    /**
     *
     */
    const MESSAGE = 'message';
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
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
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
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setQuickrfqId($quickrfq_id);
    /**
     * Get customer_id
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer_id
     * @param int $customer_id
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setCustomerId($customer_id);
    /**
     * Get is_main
     * @return int|null
     */
    public function getIsMain();

    /**
     * Set is_main
     * @param int $is_main
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setIsMain($is_main);
    /**
     * Get message
     * @return string|null
     */
    public function getMessage();

    /**
     * Set message
     * @param string $message
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setMessage($message);
    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $created_at
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setCreatedAt($created_at);

    /**
     * Get attachment
     * @return \Lof\Quickrfq\Api\Data\AttachmentInterface|null
     */
    public function getAttachment();

    /**
     * Set attachment
     * @param \Lof\Quickrfq\Api\Data\AttachmentInterface|null $attachment
     * @return \Lof\Quickrfq\Api\Data\MessageInterface
     */
    public function setAttachment($attachment);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Quickrfq\Api\Data\MessageExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Quickrfq\Api\Data\MessageExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Quickrfq\Api\Data\MessageExtensionInterface $extensionAttributes
    );
}
