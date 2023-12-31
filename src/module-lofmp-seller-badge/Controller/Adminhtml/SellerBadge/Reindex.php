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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Controller\Adminhtml\SellerBadge;

use Lofmp\SellerBadge\Model\Indexer\SellerBadgeManagerIndexer;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Exception;

class Reindex extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Lofmp_SellerBadge::SellerBadge_update';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SellerBadgeManagerIndexer
     */
    private $_sellerBadgeManagerIndexer;

    /**
     * Reindex constructor.
     * @param Context $context
     * @param SellerBadgeManagerIndexer $sellerBadgeManagerIndexer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        SellerBadgeManagerIndexer $sellerBadgeManagerIndexer,
        LoggerInterface $logger
    ) {
        $this->_sellerBadgeManagerIndexer = $sellerBadgeManagerIndexer;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     * @throws \Throwable
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_sellerBadgeManagerIndexer->executeRow($id);
                $this->messageManager->addSuccessMessage(__('The Seller Badge has been re-indexed.'));
                $this->_redirect('*/*/edit', ['badge_id' => $id]);
                return;
            } catch (LocalizedException $e) {
                $this->_redirect('*/*/edit', ['badge_id' => $id]);
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->logger->critical($e);
                $this->_redirect('*/*/edit', ['badge_id' => $id]);
                $this->messageManager->addExceptionMessage($e, __('There was a problem with reindexing process.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Something went wrong.'));
        }
    }
}
