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

namespace Lof\Quickrfq\Block\Adminhtml\Rfq;

use Lof\Quickrfq\Model\Attachment;
use Lof\Quickrfq\Model\Quickrfq;
use Lof\Quickrfq\Model\ResourceModel\Message\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Message
 *
 * @package Lof\Quickrfq\Block\Adminhtml\Rfq
 */
class Message extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var Collection
     */
    private $messageCollection;
    /**
     * @var UrlInterface
     */
    private $_urlInterface;
    /**
     * @var Attachment
     */
    private $attachment;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Message constructor.
     * @param Context $context
     * @param UrlInterface $urlInterface
     * @param Collection $messageCollection
     * @param Quickrfq $quickrfq
     * @param Attachment $attachment
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlInterface,
        Collection $messageCollection,
        Quickrfq $quickrfq,
        Attachment $attachment,
        ProductRepository $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quickrfq = $quickrfq;
        $this->attachment = $attachment;
        $this->_urlInterface = $urlInterface;
        $this->productRepository = $productRepository;
        $this->messageCollection = $messageCollection;
    }

    /**
     * @return Collection
     */
    public function getMessageCollection()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->messageCollection->addFieldToFilter('quickrfq_id', $quoteId)->setOrder('created_at', 'ASC');
    }

    /**
     * @return mixed
     */
    public function getQuoteId()
    {
        return $this->getRequest()->getParam('quickrfq_id');
    }

    /**
     * @return Quickrfq
     */
    public function getQuote()
    {
        return $this->quickrfq->load($this->getQuoteId());
    }

    /**
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed|null
     */
    public function getProduct()
    {
        $productId = $this->getQuote()->getProductId();
        try {
            $product = $this->productRepository->getById($productId);
            return ! empty($product->getId()) ? $product : null;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param $message
     * @return bool
     */
    public function isCustomer($message)
    {
        if ($message->getCustomerId() != 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $message
     * @return \Magento\Framework\Phrase
     */
    public function getContactName($message)
    {
        $quoteId = $message->getQuickrfqId();
        if ($quoteId) {
            return $this->quickrfq->load($quoteId)->getContactName();
        } else {
            return __('Customer');
        }
    }

    /**
     * @param $message
     * @return \Magento\Framework\Phrase
     */
    public function getSendertName($message)
    {
        $quoteId = $message->getQuickrfqId();

        if ($quoteId && $message->getCustomerId() || $message->getIsMain()) {
            return $this->quickrfq->load($quoteId)->getContactName();
        } else {
            return __('You');
        }
    }

    /**
     * @return string
     */
    public function getSendMessageLink()
    {
        return $this->_urlInterface->getUrl('*/*/send', [ 'quickrfq_id' => $this->getQuoteId() ]);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getAttachFiles()
    {
        $quoteId = $this->getRequest()->getParam('quickrfq_id');
        return $this->attachment->getCollection()->addFieldToFilter('quickrfq_id', $quoteId);
    }

    /**
     * @param $attachmentId
     * @return string
     */
    public function getAttachmentUrl($attachmentId)
    {
        return $this->_urlInterface->getUrl('*/*/download', [ 'attachment_id' => $attachmentId ]);
    }
}
