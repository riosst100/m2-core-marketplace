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

namespace Lof\Quickrfq\Model\Attachment;

use Lof\Quickrfq\Model\Attachment\File;

/**
 * Class DownloadProvider
 */
class DownloadProvider
{
    /**
     * Comment attachment factory
     *
     * @var \Lof\Quickrfq\Model\AttachmentFactory
     */
    private $attachmentFactory;

    /**
     * File
     *
     * @var \Lof\Quickrfq\Model\Attachment\File
     */
    private $file;


    /**
     * @var
     */
    private $attachmentId;


    /**
     * DownloadProvider constructor.
     * @param \Lof\Quickrfq\Model\AttachmentFactory $attachmentFactory
     * @param \Lof\Quickrfq\Model\Attachment\File $file
     * @param $attachmentId
     */
    public function __construct(
        \Lof\Quickrfq\Model\AttachmentFactory $attachmentFactory,
        \Lof\Quickrfq\Model\Attachment\File $file,
        $attachmentId
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->file = $file;
        $this->attachmentId = $attachmentId;
    }

    /**
     * Get attachment contents
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\NotFoundException
     * @return void
     */
    public function getAttachmentContents()
    {
        $attachment = $this->attachmentFactory->create()->load($this->attachmentId);

        if ($attachment && $attachment->getId() === null) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }

        $this->file->downloadContents($attachment);
    }
}
