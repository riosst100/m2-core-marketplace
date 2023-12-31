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

namespace Lof\MarketPlace\Block\Sale\Shipment;

use Lof\MarketPlace\Model\OrderFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\SellerProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection
     */
    protected $invoice;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;
    /**
     * @var string[]
     */
    protected $states;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

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
     * @var \Lof\MarketPlace\Model\Order
     */
    protected $order;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    public $helper;

    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProductFactory;

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice
     * @param \Lof\MarketPlace\Model\Order $order
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param PriceCurrencyInterface $priceFormatter
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param SellerFactory $sellerFactory
     * @param SellerProductFactory $sellerProductFactory
     * @param OrderFactory $orderFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice,
        \Lof\MarketPlace\Model\Order $order,
        \Lof\MarketPlace\Model\Seller $seller,
        InvoiceRepositoryInterface $invoiceRepository,
        PriceCurrencyInterface $priceFormatter,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Payment\Helper\Data $paymentData,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        SellerFactory $sellerFactory,
        SellerProductFactory $sellerProductFactory,
        OrderFactory $orderFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderitems = $orderitems;
        $this->order = $order;
        $this->helper = $helper;
        $this->groupRepository = $groupRepository;
        $this->request = $context->getRequest();
        $this->states = $invoiceRepository->create()->getStates();
        $this->priceFormatter = $priceFormatter;
        $this->invoice = $invoice;
        $this->seller = $seller;
        $this->addressRenderer = $addressRenderer;
        $this->session = $customerSession;
        $this->_paymentData = $paymentData;
        $this->orderFactory = $orderFactory;
        $this->sellerFactory = $sellerFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * @return string|void|null
     */
    public function getFormattedAddress()
    {
        if ($this->getOrder()->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder()->getShippingAddress(), 'html');
        } else {
            return;
        }
    }

    /**
     * @return string|null
     */
    public function getBillingAddress()
    {
        return $this->addressRenderer->format($this->getOrder()->getBillingAddress(), 'html');
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * @return \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection
     */
    public function getInvoiceCollection()
    {
        $invoiceCollection = $this->invoice;
        return $invoiceCollection;
    }

    /**
     * @param $status
     * @return mixed
     */
    public function getStatus($status)
    {
        return isset($this->states[$status]) ? $this->states[$status]->getText() : $status;
    }

    /**
     * Get object created at date
     *
     * @param string $createdAt
     * @return mixed
     */
    public function getOrderAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getOrder()) {
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * Get All Carriers
     *
     * @return array
     */
    public function getCarriers()
    {
        $carriers = [];
        $carrierInstances = $this->_getCarriersInstances();
        $carriers['custom'] = __('Custom Value');
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        return $carriers;
    }

    /**
     * @return array
     */
    protected function _getCarriersInstances()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $shippingConfig = $objectManager->create(\Magento\Shipping\Model\Config::class);
        return $shippingConfig->getAllCarriers($this->getShipment()->getStoreId());
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPayment()
    {
        $paymentInfoBlock = $this->_paymentData->getInfoBlock($this->getOrder()->getPayment(), $this->getLayout());
        $this->setChild('info', $paymentInfoBlock);
        $this->setData('payment', $this->getOrder()->getPayment());
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDate()
    {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getOrder()->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }

    /**
     * @param $productId
     * @return \Magento\Framework\DataObject
     */
    public function getOrderItems($productId)
    {
        $orderitems = $this->orderitems->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->addFieldToFilter('order_id', $this->getInvoice()->getOrderId())
            ->addFieldToFilter('product_id', $productId)
            ->getFirstItem();

        return $orderitems;
    }

    /**
     * @return array|mixed|null
     */
    public function getShipment()
    {
        if (!$this->hasData('mp_current_shipment')) {
            $this->setData('mp_current_shipment', $this->_coreRegistry->registry('mp_current_shipment'));
        }
        return $this->getData('mp_current_shipment');
    }

    /**
     * @return array|mixed|null
     */
    public function getOrder()
    {
        if (!$this->hasData('mp_current_order')) {
            $this->setData('mp_current_order', $this->_coreRegistry->registry('mp_current_order'));
        }
        return $this->getData('mp_current_order');
    }

    /**
     * @param $price
     * @param $base_currency_code
     * @return string
     */
    public function getPriceFomat($price, $base_currency_code)
    {
        $currencyCode = isset($base_currency_code) ? $base_currency_code : null;
        return $this->priceFormatter->format(
            $price,
            false,
            null,
            null,
            $currencyCode
        );
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
            $sellerDatas = $this->sellerFactory->create()->load($customerId, 'customer_id');
            $status = $sellerDatas->getStatus();
            return $status;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $sellerDatas = $this->sellerFactory->create()->load($customerId, 'customer_id');
            $id = $sellerDatas->getId();
            return $id;
        }

        return false;
    }

    /**
     * @param $productid
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSeller($productid)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productid, 'entity_id');
        $seller_id = $product->getSellerId();
        $sellerDatas = $this->sellerFactory->create()->load($seller_id, 'seller_id');

        return $sellerDatas;
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
        $this->pageConfig->getTitle()->set(__('View Shipment'));
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getSellerProducts()
    {
        $sellerId = $this->getSellerId();
        $productIds = [];
        if ($sellerId) {
            $productModel = $this->sellerProductFactory->create()->getCollection();
            $products = $productModel->addFieldToFilter("seller_id", $sellerId)->load();
            foreach ($products as $product) {
                array_push($productIds, $product->getData("product_id"));
            }
        }
        return $productIds;
    }

    /**
     * @param string $code
     * @return \Magento\Framework\Phrase|string|bool
     */
    public function getCarrierTitle($code)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $carrierFactory = $objectManager->create(
            \Magento\Shipping\Model\CarrierFactory::class
        );
        $carrier = $carrierFactory->create($code);
        if ($carrier) {
            return $carrier->getConfigData('title');
        } else {
            return __('Custom Value');
        }

        return false;
    }

    /**
     * @param null $orderId
     * @param null $shipmentId
     * @return string
     */
    public function trackingAddUrl($orderId = null, $shipmentId = null)
    {
        return $this->_urlBuilder->getUrl(
            'catalog/sales/add',
            [
                'order_id' => $orderId,
                'shipment_id' => $shipmentId,
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }

    /**
     * @param null $orderId
     * @param null $shipmentId
     * @param null $id
     * @return string
     */
    public function trackingDeleteUrl($orderId = null, $shipmentId = null, $id = null)
    {
        return $this->_urlBuilder->getUrl(
            'catalog/sales/delete',
            [
                'order_id' => $orderId,
                'shipment_id' => $shipmentId,
                'id' => $id,
                '_secure' => $this->getRequest()->isSecure()
            ]
        );
    }
}
