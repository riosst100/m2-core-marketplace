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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Lofmp\SellerIdentificationApproval\Api\Data\AttachmentInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Attachment extends AbstractModel implements AttachmentInterface
{

    const CACHE_TAG = 'lof_marketplace_seller_attachment';

    /**
     * @var string
     */
    protected $_cacheTag = 'lof_marketplace_seller_attachment';

    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_seller_attachment';

    /**
     * Construct init.
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Attachment::class);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [ self::CACHE_TAG . '_' . $this->getId() ];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
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
    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setFileName($fileName)
    {
        return $this->setData(self::FILE_NAME, $fileName);
    }

    /**
     * @inheritDoc
     */
    public function getFilePath()
    {
        return $this->getData(self::FILE_PATH);
    }

    /**
     * @inheritDoc
     */
    public function setFilePath($filePath)
    {
        return $this->setData(self::FILE_PATH, $filePath);
    }

    /**
     * @inheritDoc
     */
    public function getFileType()
    {
        return $this->getData(self::FILE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setFileType($fileType)
    {
        return $this->setData(self::FILE_TYPE, $fileType);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifyType()
    {
        return $this->getData(self::IDENTIFY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setIdentifyType($identifyType)
    {
        return $this->setData(self::IDENTIFY_TYPE, $identifyType);
    }

    /**
     * @inheritDoc
     */
    public function getIdentificationRequest()
    {
        return $this->getData(self::IDENTIFICATION_REQUEST);
    }

    /**
     * @inheritDoc
     */
    public function setIdentificationRequest($identificationRequest)
    {
        return $this->setData(self::IDENTIFICATION_REQUEST, $identificationRequest);
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
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
