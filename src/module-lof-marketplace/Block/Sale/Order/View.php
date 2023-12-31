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

namespace Lof\MarketPlace\Block\Sale\Order;

use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\SellerProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection
     */
    protected $invoice;

    /**
     * @var \Magento\Customer\Model\SessionFactory
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
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerProductFactory
     */
    protected $sellerProductFactory;

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
     * @var mixed|null
     */
    protected $_currentSeller = null;

    /**
     * @var mixed|array
     */
    protected $_sellers = [];

    /**
     * @var mixed|array
     */
    protected $_sellerOrderItems = [];

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var int
     */
    protected $_seller_id;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param PriceCurrencyInterface $priceFormatter
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param SellerProductFactory $sellerProductFactory
     * @param SellerFactory $sellerFactory
     * @param EventManager $eventManager
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice,
        \Lof\MarketPlace\Model\Seller $seller,
        InvoiceRepositoryInterface $invoiceRepository,
        PriceCurrencyInterface $priceFormatter,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Magento\Payment\Helper\Data $paymentData,
        SellerProductFactory $sellerProductFactory,
        SellerFactory $sellerFactory,
        EventManager $eventManager,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->orderitems = $orderitems;
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
        $this->sellerFactory = $sellerFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->eventManager = $eventManager;
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
     * @return string|void|null
     */
    public function getBillingAddress()
    {
        if ($this->getOrder()->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder()->getBillingAddress(), 'html');
        } else {
            return;
        }
    }

    /**
     * @param $product_id
     * @return \Magento\Framework\DataObject
     */
    public function getOrderItems($product_id)
    {
        $order_id = $this->getOrder()->getId();
        if (!isset($this->_sellerOrderItems[$order_id."_".$product_id])) {
            $seller = $this->getCurrentSeller();
            $this->_sellerOrderItems[$order_id."_".$product_id] = $this->orderitems->getCollection()
                ->addFieldToFilter('order_id', $order_id)
                ->addFieldToFilter('product_id', $product_id)
                ->addFieldToFilter('seller_id', $seller->getId())
                ->getFirstItem();
        }
        return $this->_sellerOrderItems[$order_id."_".$product_id];
    }

    /**
     * Get order store name
     *
     * @return null|string
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
        return $this->invoice;
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
     * @param $orderId
     * @return \Lof\MarketPlace\Model\Order
     */
    public function getSellerOrder($orderId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->get(\Lof\MarketPlace\Model\OrderFactory::class)->create()->load($orderId, 'order_id');
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
     * @return mixed
     */
    public function getOrder()
    {
        if (!$this->hasData('mp_current_order')) {
            $this->setData('mp_current_order', $this->_coreRegistry->registry('mp_current_order'));
        }
        return $this->getData('mp_current_order');
    }

    /**
     * @return \Lof\MarketPlace\Model\Order
     */
    public function getTheSellerOrder()
    {
        if (!$this->hasData('mp_current_seller_order')) {
            $this->setData('mp_current_seller_order', $this->_coreRegistry->registry('mp_current_seller_order'));
        }
        return $this->getData('mp_current_seller_order');
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
     * @param int $sellerId
     * @return object
     */
    public function getSellerById($sellerId)
    {
        if (!isset($this->_sellers[$sellerId])) {
            $this->_sellers[$sellerId] = $this->sellerFactory->create()->load($sellerId, 'seller_id');
        }
        return $this->_sellers[$sellerId];
    }

    /**
     * get Current Seller
     * @return \Lof\MarketPlace\Model\Seller|mixed|null
     */
    public function getCurrentSeller()
    {
        if (!$this->_currentSeller) {
            $customerSession = $this->session->create();
            if ($customerSession->isLoggedIn()) {
                $customerId = $customerSession->getId();
                $this->_currentSeller = $this->sellerFactory->create()->load($customerId, 'customer_id');
            }
        }
        return $this->_currentSeller;
    }

    /**
     * @return mixed
     */
    public function isSeller()
    {
        $customerSession = $this->session->create();
        if ($customerSession->isLoggedIn()) {
            $sellerDatas = $this->getCurrentSeller();
            $status = $sellerDatas ? $sellerDatas->getStatus() : 0;
            return $status;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getSellerId()
    {
        if (!isset($this->_seller_id)) {
            $customerSession = $this->session->create();
            if ($customerSession->isLoggedIn()) {
                $sellerDatas = $this->getCurrentSeller();
                $id = $sellerDatas ? $sellerDatas->getId() : 0;
                $this->_seller_id = $id;
            } else {
                $this->_seller_id = 0;
            }
        }
        return $this->_seller_id;
    }

    /**
     * @param $productid
     * @return object
     */
    public function getSeller($productid)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create(\Magento\Catalog\Model\Product::class)->load($productid, 'entity_id');
        $seller_id = $product->getSellerId();
        return $this->getSellerById($seller_id);
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
        $this->pageConfig->getTitle()->set(__('View Order'));
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
     * Check product id is one of current seller
     * Allow other module trigger event at here with event name: lof_marketplace_check_product_is_of_seller
     * Trigger Event data:
     * order_view[seller_id => int, product_id => int, order => object, is_product_of_seller => boolean]
     * @param int $product_id
     * @param int $seller_id
     * @param int $quote_id
     * @param int $item_id
     * @return boolean
     */
    public function checkProductIsOfSeller($product_id, $seller_id = 0, $quote_id = 0, $item_id = 0)
    {
        $is_product_of_seller = false;
        $sellerId = $seller_id ? (int)$seller_id : $this->getSellerId();
        if ($sellerId) {
            $collection = $this->sellerProductFactory->create()->getCollection()
                ->addFieldToFilter("product_id", (int)$product_id)
                ->addFieldToFilter("seller_id", $sellerId);

            if ($collection->count()) {
                $is_product_of_seller = true;
            }
        }
        $eventData = [
            "seller_id" => $sellerId,
            "product_id" => $product_id,
            "order" => $this->getOrder(),
            "quote_id" => $quote_id,
            "item_id" => $item_id,
            "is_product_of_seller" => $is_product_of_seller
        ];
        $this->setData("marketplace_event_data", $eventData);
        $this->eventManager->dispatch('lof_marketplace_check_product_is_of_seller', ['order_view' => $eventData]);
        return $is_product_of_seller;
    }
}
