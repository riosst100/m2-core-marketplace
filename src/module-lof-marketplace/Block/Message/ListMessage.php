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

class ListMessage extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var
     */
    protected $_messages;

    /**
     * ListMessage constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Message $message
     * @param \Lof\MarketPlace\Model\MessageDetail $detail
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerSession = $customerSession;
        $this->message = $message;
        $this->detail = $detail;
        $this->httpContext = $httpContext;
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
        $conditions = '';
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
     * @return string
     */
    public function getNewActionUrl()
    {
        return $this->getUrl('lofmarketplace/customer/viewmessage/new');
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getMessages()
    {
        if (!isset($this->_messages)) {
            $this->_messages = $this->message->getCollection()
                ->addFieldToFilter('owner_id', ['gt' => 0])
                ->addFieldToFilter('sender_id', $this->_customerSession->getCustomerId())
                ->setOrder("created_at", "DESC");
        }

        return $this->_messages;
    }

    /**
     * @return ListMessage
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__("My Messages"));
        if ($this->getMessages()) {
            $pager = $this->getLayout()
                ->createBlock(
                    \Magento\Theme\Block\Html\Pager::class,
                    'quotation.quote.history.pager'
                )
                ->setCollection($this->getMessages());
            $this->setChild('pager', $pager);
            $this->getMessages()->load();
        }
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param int $message_id
     * @return string
     */
    public function getUnreadMessage($message_id)
    {
        $collection = $this->message->getCollection()
            ->addFieldToFilter('message_id', $message_id)
            ->addFieldToFilter('is_read', 0)
            ->addFieldToFilter('receiver_id', $this->_customerSession->getCustomerId());

        if ($collection->count() > 0) {
            $message = __('Unread');
        } else {
            $message = __('Read');
        }

        return $message;
    }

    /**
     * @return string
     */
    public function getMessageUrl()
    {
        return $this->getUrl('customer/message');
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
}
