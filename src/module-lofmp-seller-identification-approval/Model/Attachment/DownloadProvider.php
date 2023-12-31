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

namespace Lofmp\SellerIdentificationApproval\Model\Attachment;

use Lofmp\SellerIdentificationApproval\Model\Attachment\File;
use Lofmp\SellerIdentificationApproval\Model\AttachmentFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

class DownloadProvider
{
    /**
     * Comment attachment factory
     *
     * @var AttachmentFactory
     */
    private $attachmentFactory;

    /**
     * File
     *
     * @var \Lofmp\SellerIdentificationApproval\Model\Attachment\File
     */
    private $file;

    /**
     * @var
     */
    private $attachmentId;

    /**
     * DownloadProvider constructor.
     * @param AttachmentFactory $attachmentFactory
     * @param \Lofmp\SellerIdentificationApproval\Model\Attachment\File $file
     * @param $attachmentId
     */
    public function __construct(
        AttachmentFactory $attachmentFactory,
        \Lofmp\SellerIdentificationApproval\Model\Attachment\File $file,
        $attachmentId
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->file = $file;
        $this->attachmentId = $attachmentId;
    }

    /**
     * Get attachment contents
     *
     * @return void
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function getAttachmentContents()
    {
        $attachment = $this->attachmentFactory->create()->load($this->attachmentId);

        if ($attachment && $attachment->getId() === null) {
            throw new NoSuchEntityException();
        }

        $this->file->downloadContents($attachment);
    }
}
