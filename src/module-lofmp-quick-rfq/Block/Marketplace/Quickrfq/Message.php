<?php
namespace Lofmp\Quickrfq\Block\Marketplace\Quickrfq;

use Magento\Framework\View\Element\Template\Context;

/**
 * Class Message
 *
 * @package Lofmp\Quickrfq\Block\Marketplace\Quickrfq
 */
class Message extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lof\Quickrfq\Model\Quickrfq
     */
    private $quickrfq;
    /**
     * @var \Lof\Quickrfq\Model\ResourceModel\Message\Collection
     */
    private $messageCollection;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $_urlInterface;
    /**
     * @var \Lof\Quickrfq\Model\Attachment
     */
    private $attachment;

    /**
     * Message constructor.
     * @param Context $context
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Lof\Quickrfq\Model\ResourceModel\Message\Collection $messageCollection
     * @param \Lof\Quickrfq\Model\Quickrfq $quickrfq
     * @param \Lof\Quickrfq\Model\Attachment $attachment
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\UrlInterface $urlInterface,
        \Lof\Quickrfq\Model\ResourceModel\Message\Collection $messageCollection,
        \Lof\Quickrfq\Model\Quickrfq $quickrfq,
        \Lof\Quickrfq\Model\Attachment $attachment,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->quickrfq = $quickrfq;
        $this->attachment = $attachment;
        $this->_urlInterface          = $urlInterface;
        $this->messageCollection = $messageCollection;
    }

    /**
     * @return \Lof\Quickrfq\Model\ResourceModel\Message\Collection
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
