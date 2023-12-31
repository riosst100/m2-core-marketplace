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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Message;

class ViewMessage extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Lof\MarketPlace\Model\MessageFactory
     */
    protected $message;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetail
     */
    protected $detail;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var mixed
     */
    protected $_customer;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\MessageDetail\Collection
     */
    protected $detailCollection = null;

    /**
     * @var \Lof\MarketPlace\Model\Message
     */
    protected $messageModel = null;

    /**
     * ViewMessage constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Message $message
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\MessageDetail $detail
     * @param \Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Magento\Framework\App\Http\Context $httpContext,
        \Lof\MarketPlace\Model\ResourceModel\MessageDetail\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->seller = $seller;
        $this->_customerSession = $customerSession;
        $this->message = $message;
        $this->detail = $detail;
        $this->httpContext = $httpContext;
        $this->collectionFactory = $collectionFactory;
        $this->request = $context->getRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 0,
            'cache_tags' => ['lof_marketplace_list_message']
        ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $conditions = "";
        return [
            'LOF_MARKETPLACE_LIST_MESSAGE',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            $conditions
        ];
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {
        return null;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSeller()
    {
        return $this->seller->getCollection()
            ->addFieldToFilter('seller_id', $this->getMessage()->getData('owner_id'))
            ->getFirstItem();
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getMessage()
    {
        if (!$this->messageModel) {
            $this->messageModel = $this->message->getCollection()
                        ->addFieldToFilter('message_id', $this->getId())
                        ->addFieldToFilter('sender_id', $this->_customerSession->getCustomerId())
                        ->getFirstItem();
        }
        return $this->messageModel;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getDetail()
    {
        $message = $this->getMessage();
        if (!$message->getId()) {
            return null;
        }
        return $this->collectionFactory->create()
                ->addFieldToFilter('message_id', $message->getId())
                ->setOrder('detail_id', 'desc');
    }

    /**
     * Get customer
     *
     * @return mixed
     */
    public function getCustomer()
    {
        if (!isset($this->_customer)) {
            $this->_customer = $this->_customerSession->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * get current message id
     * @return int
     */
    public function getId()
    {
        $messageId = $this->request->getParam("message_id");
        return (int)$messageId;
    }

    /**
     * save is_read for message
     *
     * @return void
     */
    public function isRead()
    {
        $message = $this->getMessage();
        if ($message) {
            $collection = $this->getDetail();
            if ($collection) {
                $collection->addFieldToFilter('receiver_id', $this->_customerSession->getCustomerId());
                foreach ($collection as $detail) {
                    $detail->setData('is_read', 1)->save();
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getMessageUrl()
    {
        return $this->getUrl('customer/message');
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {

        $this->isRead();

        return parent::_beforeToHtml();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        return parent::toHtml();
    }

    /**
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__("My Messages"));
        return parent::_prepareLayout();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getMessages()
    {
        return $this->message->getCollection()
            ->addFieldToFilter('sender_id', $this->_customerSession->getCustomerId());
    }

    /**
     * @param $message_id
     * @return string
     */
    public function getUnreadMessage($message_id)
    {
        $unread = $this->message->getCollection()
            ->addFieldToFilter('message_id', $message_id)
            ->addFieldToFilter('receiver_id', $this->_customerSession->getCustomerId())
            ->addFieldToFilter('is_read', 0);

        if (count($unread) > 0) {
            $count = __('Unread');
        } else {
            $count = __('Read');
        }

        return $count;
    }
}
