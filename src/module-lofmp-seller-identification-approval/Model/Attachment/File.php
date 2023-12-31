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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class File
{
    /**
     * Filesystem driver
     *
     * @var Filesystem\Driver\File
     */
    private $fileDriver;

    /**
     * File factory
     *
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * Media directory
     *
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * DownloadProvider constructor
     * @param Filesystem\Driver\File $fileDriver
     * @param FileFactory $fileFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Filesystem\Driver\File $fileDriver,
        FileFactory $fileFactory,
        Filesystem $filesystem
    ) {
        $this->fileDriver = $fileDriver;
        $this->fileFactory = $fileFactory;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * @param $attachment
     * @throws FileSystemException
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
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
    }
}
