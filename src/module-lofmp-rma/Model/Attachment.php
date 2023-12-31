<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Attachment extends \Magento\Framework\Model\AbstractModel implements IdentityInterface, \Lofmp\Rma\Api\Data\AttachmentInterface
{
    const CACHE_TAG = 'rma_attachment';

    /**
     * {@inheritdoc}
     */
    protected $_cacheTag = 'rma_attachment';

    /**
     * {@inheritdoc}
     */
    protected $_eventPrefix = 'rma_attachment';

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\ResourceModel\Attachment');
    }

    /**
     * {@inheritdoc}
     */
    public function getItemType()
    {
        return $this->getData(self::KEY_ITEM_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemType($itemType)
    {
        return $this->setData(self::KEY_ITEM_TYPE, $itemType);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::KEY_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::KEY_ITEM_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->getData(self::KEY_UID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUid($uid)
    {
        return $this->setData(self::KEY_UID, $uid);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::KEY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->getData(self::KEY_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($size)
    {
        return $this->setData(self::KEY_SIZE, $size);
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->getData(self::KEY_BODY);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body)
    {
        return $this->setData(self::KEY_BODY, $body);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::KEY_CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::KEY_CREATED_AT, $createdAt);
    }
}
