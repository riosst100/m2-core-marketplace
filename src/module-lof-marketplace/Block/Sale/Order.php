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
class Order extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $sellerFactory;

    /**
     * @var
     */
    protected $invoice;

    /**
     * Order constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Order $order
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Order $order,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        array $data = []
    ) {
        $this->order = $order;
        $this->sellerFactory = $sellerFactory;
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
     * @return mixed
     */
    public function isSeller()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->sellerFactory->create()->load('customer_id', $customerId)->getStatus();
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
     * @param $orderId
     * @return mixed
     */
    public function getOrderData($orderId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(\Magento\Sales\Model\Order::class)->load($orderId);
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
            $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
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
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Order'));
        return parent::_prepareLayout();
    }
}
