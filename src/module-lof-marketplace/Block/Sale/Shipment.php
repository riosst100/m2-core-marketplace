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

namespace Lof\MarketPlace\Block\Sale;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Shipment extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Model\OrderFactory
     */
    protected $order;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $_shipmentCollection;

    /**
     * @var
     */
    protected $invoice;

    /**
     * Shipment constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Order $order
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollection
     * @param \Lof\MarketPlace\Model\SellerFactory $seller
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Order $order,
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $shipmentCollection,
        \Lof\MarketPlace\Model\SellerFactory $seller,
        array $data = []
    ) {
        $this->_shipmentCollection = $shipmentCollection;
        $this->order = $order;
        $this->seller = $seller;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getInvoiceCollection()
    {
        return $this->invoice;
    }

    /**
     * @param $order_id
     * @return \Magento\Framework\DataObject
     */
    public function getShipmentsByOrderId($order_id)
    {
        return $this->_shipmentCollection->create()
            ->addFieldToFilter('order_id', $order_id)
            ->setOrder('entity_id', 'DESC')
            ->getFirstItem();
    }

    /**
     * @return mixed
     */
    public function isSeller()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->seller->create()->load('customer_id', $customerId)->getStatus();
            return $status;
        }

        return false;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getOrder()
    {
        return $this->order->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
    }

    /**
     * @param $date
     * @return string
     */
    public function getOrderDate($date)
    {
        return $this->formatDate(
            $this->getOrderAdminDate($date),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }

    /**
     * @param $createdAt
     * @return \DateTime
     * @throws \Exception
     */
    public function getOrderAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function getOrderData($order_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(\Magento\Sales\Model\Order::class)->load($order_id);
    }

    /**
     * @return array|mixed|null
     */
    public function getSellerId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $seller = $this->seller->create()->load($customerId, 'customer_id');
            return $seller->getData('seller_id');
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return Shipment
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Manage Shipments'));
        return parent::_prepareLayout();
    }
}
