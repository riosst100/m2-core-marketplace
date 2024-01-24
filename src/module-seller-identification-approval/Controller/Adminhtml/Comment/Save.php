<?php

namespace CoreMarketplace\SellerIdentificationApproval\Controller\Adminhtml\Comment;

use Lofmp\SellerIdentificationApproval\Model\Attachment\DownloadProviderFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Save extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * Download provider factory
     * @var DownloadProviderFactory
     */
    private $downloadProviderFactory;

    /**
     * DownloadIdentify constructor.
     * @param Context $context
     * @param DownloadProviderFactory $downloadProviderFactory
     */
    public function __construct(
        Context $context,
        DownloadProviderFactory $downloadProviderFactory
    ) {
        parent::__construct($context);
        $this->downloadProviderFactory = $downloadProviderFactory;
    }

    /**
     * Execute
     * @return ResponseInterface
     */
    public function execute()
    {
        $attachmentId = $this->getRequest()->getParam('attachment_id');
        /** @var DownloadProvider $downloadProvider */
        $downloadProvider = $this->downloadProviderFactory->create(['attachmentId' => $attachmentId]);

        try {
            $downloadProvider->getAttachmentContents();
        } catch (\Exception $e) {
            $this->messageManager->addNoticeMessage(__('We can\'t find the file you requested.'));
            return $this->_redirect('*/*/');
        }
    }
}
