<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\SellerIdentificationApproval\Api\Data;

interface AttachmentInterface
{

    const SELLER_ID = 'seller_id';
    const IDENTIFICATION_REQUEST = 'identification_request';
    const UPDATED_AT = 'updated_at';
    const IDENTIFY_TYPE = 'identify_type';
    const FILE_NAME = 'file_name';
    const ENTITY_ID = 'entity_id';
    const FILE_TYPE = 'file_type';
    const CREATED_AT = 'created_at';
    const FILE_PATH = 'file_path';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setEntityId($entityId);

    /**
     * Get seller_id
     * @return string|null
     */
    public function getSellerId();

    /**
     * Set seller_id
     * @param string $sellerId
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get file_name
     * @return string|null
     */
    public function getFileName();

    /**
     * Set file_name
     * @param string $fileName
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setFileName($fileName);

    /**
     * Get file_path
     * @return string|null
     */
    public function getFilePath();

    /**
     * Set file_path
     * @param string $filePath
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setFilePath($filePath);

    /**
     * Get file_type
     * @return string|null
     */
    public function getFileType();

    /**
     * Set file_type
     * @param string $fileType
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setFileType($fileType);

    /**
     * Get identify_type
     * @return string|null
     */
    public function getIdentifyType();

    /**
     * Set identify_type
     * @param string $identifyType
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setIdentifyType($identifyType);

    /**
     * Get identification_request
     * @return string|null
     */
    public function getIdentificationRequest();

    /**
     * Set identification_request
     * @param string $identificationRequest
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setIdentificationRequest($identificationRequest);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
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
     * @return \Lofmp\SellerIdentificationApproval\Attachment\Api\Data\AttachmentInterface
     */
    public function setUpdatedAt($updatedAt);
}

