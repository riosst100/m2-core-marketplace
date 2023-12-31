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

namespace Lof\MarketPlace\Block\Seller\Message;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdminView extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Lof\MarketPlace\Model\Message
     */
    protected $message;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetail
     */
    protected $detail;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    public $helper;

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * AdminView constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\MessageAdmin $message
     * @param \Lof\MarketPlace\Model\MessageDetail $detail
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\MessageAdmin $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->detail = $detail;
        $this->message = $message;
        $this->helper = $helper;
        $this->groupRepository = $groupRepository;
        $this->request = $context->getRequest();
        $this->seller = $seller;
        $this->session = $customerSession;
        $this->_paymentData = $paymentData;
    }

    /**
     * @return \Lof\MarketPlace\Model\Message|\Lof\MarketPlace\Model\MessageAdmin
     */
    public function getMessage()
    {
        return $this->message->load($this->getMessageId());
    }

    /**
     * @return mixed|string
     */
    public function getMessageId()
    {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return $params[5];
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSeller()
    {
        return $this->seller->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getFirstItem();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getDetail()
    {
        return $this->detail->getCollection()
            ->addFieldToFilter('message_id', $this->getMessageId())
            ->addFieldToFilter('message_admin', 1)
            ->setOrder('detail_id', 'desc');
    }

    /**
     * @throws \Exception
     */
    public function isRead()
    {
        $sellerId = $this->getSeller()->getData('seller_id');
        $collection = $this->getDetail()->addFieldToFilter('receiver_id', $sellerId);
        foreach ($collection as $detail) {
            $detail->setData('is_read', 1)->save();
        }

        $message = $this->getMessage()->setData('is_read', 1);
        $message->save();
    }

    /**
     * @return AdminView
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set($this->getMessage()->getSubject());
        return parent::_prepareLayout();
    }
}
