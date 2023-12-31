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

/**
 * Class File
 */
class File
{
    /**
     * Filesystem driver
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * File factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    private $fileFactory;

    /**
     * Media directory
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * DownloadProvider constructor
     *
     * @param \Magento\Framework\Filesystem\Driver\File $fileDriver
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->fileDriver = $fileDriver;
        $this->fileFactory = $fileFactory;
        $this->mediaDirectory = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }


    /**
     * @param $attachment
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function downloadContents($attachment)
    {
        $fileName = $attachment->getFileName();
        $attachmentPath = $this->mediaDirectory
                ->getAbsolutePath(UploadHandler::ATTACHMENTS_FOLDER)
            . $attachment->getFilePath();
        $fileSize = isset($this->fileDriver->stat($attachmentPath)['size'])
            ? $this->fileDriver->stat($attachmentPath)['size']
            : 0;

        $this->fileFactory->create(
            $fileName,
            $this->fileDriver->fileGetContents($attachmentPath),
            \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
    }
}
