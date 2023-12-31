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

namespace Lofmp\SellerIdentificationApproval\Observer;

use Exception;
use Lofmp\SellerIdentificationApproval\Model\Attachment\UploaderFactory;
use Lofmp\SellerIdentificationApproval\Model\AttachmentFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem\File\ReadFactory;

class SaveAttachment implements ObserverInterface
{
    const BASE64_ENCODED_DATA = 'base64_encoded_data';

    const ATTACHMENTS_FOLDER = 'lof_seller';
    /**
     * @var AttachmentFactory
     */
    private $attachmentFactory;
    /**
     * @var ReadFactory
     */
    private $readFactory;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;
    /**
     * @var File
     */
    private $file;

    /**
     * SaveAttachment constructor.
     * @param AttachmentFactory $attachmentFactory
     * @param ReadFactory $readFactory
     * @param Filesystem $filesystem
     * @param UploaderFactory $uploaderFactory
     * @param File $file
     */
    public function __construct(
        AttachmentFactory $attachmentFactory,
        ReadFactory $readFactory,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        File $file
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->readFactory       = $readFactory;
        $this->filesystem        = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->file = $file;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws FileSystemException
     */
    public function execute(Observer $observer)
    {
        $seller = $observer->getData('seller');

        $request = $observer->getData('account_controller')->getRequest();

        $identityRequest = $request->getParam('identification');
        $seller->setData('identification_request', $identityRequest);
        $seller->save();

        $filesArray = (array)$request->getFiles();
        $deleteIds = $request->getParam('delete_ids');
        if ($deleteIds) {
            $this->deleteItems($deleteIds);
        }

        $files = $this->getFiles($filesArray);

        if ($files) {
            foreach ($files as $file) {
                $this->saveSellerAttachment($file, $seller->getData('seller_id'));
            }
        }
        return $this;
    }

    /**
     * @param $filesArray
     * @return array
     */
    public function getFiles($filesArray)
    {
        $files = [];
        foreach ($filesArray as $key => $fileTypes) {
            foreach ($fileTypes as $file) {
                if (empty($file['tmp_name'])) {
                    continue;
                }
                $fileContent = $this->readFactory
                    ->create($file['tmp_name'], \Magento\Framework\Filesystem\DriverPool::FILE)
                    ->read($file['size']);
                $fileContent = base64_encode($fileContent);
                $files[] = $this->attachmentFactory->create(
                    [
                        'data' => [
                            'base64_encoded_data' => $fileContent,
                            'type'                => $file['type'],
                            'name'                => $file['name'],
                            'file_type'           => str_replace("-files", "", $key)
                        ],
                    ]
                );
            }
        }
        return $files;
    }

    /**
     * @param $file
     * @param $sellerId
     * @throws FileSystemException
     * @throws Exception
     */
    public function saveSellerAttachment($file, $sellerId)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $fileContent = base64_decode($file->getData(self::BASE64_ENCODED_DATA), true);
        $tmpDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::SYS_TMP);
        $tmpFileName  = substr(hash('sha256', (rand())), 0, 7) . '.' . $file->getName();
        $tmpDirectory->writeFile($tmpFileName, $fileContent);

        $fileAttributes = [
            'tmp_name' => $tmpDirectory->getAbsolutePath() . $tmpFileName,
            'name'     => $file->getName(),
            'file_type'     => $file->getFileType(),
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
        )->getAbsolutePath(self::ATTACHMENTS_FOLDER);
        $data = $uploader->save($path);

        if (isset($data['name']) && isset($data['file'])) {
            $attachment = $this->attachmentFactory->create();
            $attachment->setSellerId($sellerId)
                ->setFileName($data['name'])
                ->setFilePath($data['file'])
                ->setFileType($file->getType())
                ->setIdentifyType($file->getFileType())
                ->save();
        }
    }

    /**
     * @param $ids
     * @throws FileSystemException
     */
    public function deleteItems($ids)
    {
        $ids = explode(',', $ids);
        $collection = $this->attachmentFactory->create()->getCollection()
            ->addFieldToFilter('entity_id', ['in' => $ids]);
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::ATTACHMENTS_FOLDER);
        foreach ($collection as $item) {
            $filePath = $item->getFilePath();
            if ($this->file->isExists($path . $filePath)) {
                $this->file->deleteFile($path . $filePath);
            }
            $item->delete();
        }
    }
}
