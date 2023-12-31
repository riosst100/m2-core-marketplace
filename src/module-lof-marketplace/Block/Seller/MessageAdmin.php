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

namespace Lof\MarketPlace\Block\Seller;

class MessageAdmin extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_groupFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Lof\MarketPlace\Model\MessageAdmin
     */
    protected $message;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetail
     */
    protected $detail;

    /**
     * MessageAdmin constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Model\Group $groupFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\MessageAdmin $message
     * @param \Lof\MarketPlace\Model\MessageDetail $detail
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Model\Group $groupFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\MessageAdmin $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->detail = $detail;
        $this->message = $message;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_groupFactory = $groupFactory;
        $this->_resource = $resource;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerCollection()
    {
        return $this->_sellerFactory->getCollection();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGroupCollection()
    {
        return $this->_groupFactory->getCollection();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getMessage()
    {
        return $this->message->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSeller()
    {
        return $this->_sellerFactory->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getFirstItem();
    }

    /**
     * @param int $message_id
     * @param int|null $receiver_id
     * @return string
     */
    public function getUnreadMessage($message_id, $receiver_id = 0)
    {
        $collection = $this->message->getCollection()
            ->addFieldToFilter('message_id', $message_id)
            ->addFieldToFilter('is_read', 0);
        if ($receiver_id) {
            $collection->addFieldToFilter('receiver_id', $this->getSeller()->getSellerId());
        } else {
            $collection->addFieldToFilter('receiver_id', 0);
        }

        if ($collection->count() > 0) {
            $message = __('Unread');
        } else {
            $message = __('Read');
        }

        return $message;
    }

    /**
     * @return mixed|string
     */
    public function getSellerId()
    {
        $seller_id = '';
        $seller = $this->_sellerFactory->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())->getData();
        foreach ($seller as $_seller) {
            $seller_id = $_seller['seller_id'];
        }

        return $seller_id;
    }

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Message'));
        return parent::_prepareLayout();
    }
}
