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

namespace Lof\Quickrfq\Controller\Quickrfq;

use Lof\Quickrfq\Model\Attachment\DownloadProviderFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Psr\Log\LoggerInterface;

/**
 * Class Download
 * @package Lof\Quickrfq\Controller\Quickrfq
 */
class Download extends Action implements HttpGetActionInterface
{
    /**
     * Download provider factory
     *
     * @var DownloadProviderFactory
     */
    private $downloadProviderFactory;

    /**
     * Logger
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Download constructor.
     * @param Context $context
     * @param DownloadProviderFactory $downloadProviderFactory
     * @param LoggerInterface $logger
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        DownloadProviderFactory $downloadProviderFactory,
        LoggerInterface $logger,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->downloadProviderFactory = $downloadProviderFactory;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->messageManager->addNoticeMessage(__('Please sign in to download.'));
            return $this->_redirect(CustomerUrl::ROUTE_ACCOUNT_LOGIN);
        }

        $attachmentId = $this->getRequest()->getParam('attachmentId');
        /** @var DownloadProvider $downloadProvider */
        $downloadProvider = $this->downloadProviderFactory->create(['attachmentId' => $attachmentId]);

        try {
            $downloadProvider->getAttachmentContents();
        } catch (\Throwable $e) {
            $this->logger->critical($e);
            $this->messageManager->addNoticeMessage(__('We can\'t find the file you requested.'));

            return $this->_redirect('*/*/');
        }
    }
}
