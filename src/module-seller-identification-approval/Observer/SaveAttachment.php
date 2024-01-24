<?php

namespace CoreMarketplace\SellerIdentificationApproval\Observer;

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

class SaveAttachment extends \Lofmp\SellerIdentificationApproval\Observer\SaveAttachment
{
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

        parent::__construct(
            $attachmentFactory,
            $readFactory,
            $filesystem,
            $uploaderFactory,
            $file
        );
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

        // $identityRequest = $request->getParam('identification');
        // $seller->setData('identification_request', implode(",", $identityRequest));
        // $seller->save();

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
}
