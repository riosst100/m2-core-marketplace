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

namespace Lof\Quickrfq\Controller\Adminhtml\Index;

use Lof\Quickrfq\Model\Attachment\DownloadProviderFactory;
use Magento\Backend\App\Action;

/**
 * Class Download
 * @package Lof\Quickrfq\Controller\Adminhtml\Index
 */
class Download extends Action
{
    /**
     * Download provider factory
     *
     * @var DownloadProviderFactory
     */
    private $downloadProviderFactory;

    /**
     * Download constructor.
     * @param Action\Context $context
     * @param DownloadProviderFactory $downloadProviderFactory
     */
    public function __construct(
        Action\Context $context,
        DownloadProviderFactory $downloadProviderFactory
    ) {
        parent::__construct($context);
        $this->downloadProviderFactory = $downloadProviderFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        $attachmentId = $this->getRequest()->getParam('attachment_id');
        /** @var DownloadProvider $downloadProvider */
        $downloadProvider = $this->downloadProviderFactory->create(['attachmentId' => $attachmentId]);

        try {
            $downloadProvider->getAttachmentContents();
        } catch (\Throwable $e) {
            $this->messageManager->addNoticeMessage(__('We can\'t find the file you requested.'));

            return $this->_redirect('*/*/');
        }
    }
}
