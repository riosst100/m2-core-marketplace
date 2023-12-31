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

use Lofmp\SellerIdentificationApproval\Model\AttachmentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;

class UploadHandler
{
    const BASE64_ENCODED_DATA = 'base64_encoded_data';

    const ATTACHMENTS_FOLDER = 'lof_seller';

    /**
     * File system
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UploadHandler constructor.
     *
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param AttachmentFactory $attachmentFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        AttachmentFactory $attachmentFactory,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->attachmentFactory = $attachmentFactory;
        $this->logger = $logger;
    }

    /**
     * Save file and create attachment for comment.
     * @param $file
     * @param $quoteId
     * @throws FileSystemException
     */
    public function process($file, $quoteId)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $fileContent = base64_decode($file->getData(UploadHandler::BASE64_ENCODED_DATA), true);
        $tmpDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $tmpFileName = substr(hash('sha256', (rand())), 0, 7) . '.' . $file->getName();
        $tmpDirectory->writeFile($tmpFileName, $fileContent);

        $fileAttributes = [
            'tmp_name' => $tmpDirectory->getAbsolutePath() . $tmpFileName,
            'name' => $file->getName(),
        ];
        $uploader = $this->uploaderFactory->create();
        $uploader->processFileAttributes($fileAttributes);
        $uploader->addValidateCallback('nameLength', $uploader, 'validateNameLength');
        $uploader->addValidateCallback('size', $uploader, 'validateSize');
        $uploader->setAllowRenameFiles(true)
            ->setFilesDispersion(true)
            ->setAllowCreateFolders(true);
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(UploadHandler::ATTACHMENTS_FOLDER);
        $data = $uploader->save($path);

        if (isset($data['name']) && isset($data['file'])) {
            /** @var AttachmentFactory $attachment */
            $attachment = $this->attachmentFactory->create();
            $attachment->setSellerId($quoteId)
                ->setFileName($data['name'])
                ->setFilePath($data['file'])
                ->setFileType($file->getType())
                ->save();
        }
    }
}
