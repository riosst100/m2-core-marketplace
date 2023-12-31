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
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Customer;

/**
 * Class CustomerAddress
 * @package Lof\Quickrfq\Block\Adminhtml\Rfq
 */
class CustomerAddress extends Template
{
    /**
     * @var Quickrfq
     */
    private $quickrfq;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Attachment
     */
    private $attachment;
    /**
     * @var UrlInterface
     */
    private $_urlInterface;

    /**
     * ProductInfo constructor.
     * @param Template\Context $context
     * @param Customer $customer
     * @param Quickrfq $quickrfq
     * @param UrlInterface $urlInterface
     * @param Attachment $attachment
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Customer $customer,
        Quickrfq $quickrfq,
        UrlInterface $urlInterface,
        Attachment $attachment,
        array $data = []
    ) {
        $this->quickrfq = $quickrfq;
        $this->customer = $customer;
        $this->attachment = $attachment;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return Quickrfq
     */
    public function getQuote()
    {
        $quoteId =  $this->getRequest()->getParam('quickrfq_id');
        return $this->quickrfq->load($quoteId);
    }


    /**
     * @return AbstractDb|AbstractCollection|null
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
