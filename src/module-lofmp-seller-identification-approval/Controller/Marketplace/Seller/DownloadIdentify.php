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

namespace Lofmp\SellerIdentificationApproval\Controller\Marketplace\Seller;

use Lofmp\SellerIdentificationApproval\Model\Attachment\DownloadProviderFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class DownloadIdentify extends Action
{
    /**
     * Download provider factory
     *
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
     * @return ResponseInterface|ResultInterface
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
