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

namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Lof\MarketPlace\Model\Source\CommissionType;
use Lof\MarketPlace\Model\Framework\Command\AutoCreateInvoiceCommandInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderStatusChanged implements ObserverInterface
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\SellerProduct
     */
    protected $sellerProduct;

    /**
     * @var \Lof\MarketPlace\Model\CalculateCommission
     */
    protected $calculate;

    /**
     * @var \Lof\MarketPlace\Model\CalculateShippingCommission
     */
    protected $calculateShippingCommission;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Model\InvoiceOrderSaveAfter
     */
    protected $invoiceOrder;

    /**
     * @var \Lof\MarketPlace\Observer\OrderInvoice
     */
    protected $sellerOrderInvoice;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var array
     */
    protected $_orders = [];

    /**
     * @var mixed
     */
    protected $_currentOrder;

    /**
     * @param AutoCreateInvoiceCommandInterface
     */
    protected $autoCreateInvoiceCommand;

    /**
     * @var mixed|null
     */
    protected $_objectManager = null;

    /**
     * OrderStatusChanged constructor.
     *
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param Session $checkoutSession
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerProduct
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Model\InvoiceOrderSaveAfter $invoiceOrder
     * @param \Lof\MarketPlace\Observer\OrderInvoice $sellerOrderInvoice
     * @param ProductRepositoryInterface $productRepository
     * @param AutoCreateInvoiceCommandInterface $autoCreateInvoiceCommand
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\CalculateShippingCommission $calculateShippingCommission,
        Session $checkoutSession,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\InvoiceOrderSaveAfter $invoiceOrder,
        \Lof\MarketPlace\Observer\OrderInvoice $sellerOrderInvoice,
        ProductRepositoryInterface $productRepository,
        AutoCreateInvoiceCommandInterface $autoCreateInvoiceCommand
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->sender = $sender;
        $this->calculate = $calculate;
        $this->calculateShippingCommission = $calculateShippingCommission;
        $this->helper = $helper;
        $this->sellerProduct = $sellerProduct;
        $this->invoiceOrder = $invoiceOrder;
        $this->sellerOrderInvoice = $sellerOrderInvoice;
        $this->productRepository = $productRepository;
        $this->autoCreateInvoiceCommand = $autoCreateInvoiceCommand;
    }

    /**
     * Get object manager
     *
     * @return mixed
     */
    public function getObjectManager()
    {
        if (!$this->_objectManager){
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->_objectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //disable the function if the module split order was enabled
        $order = $observer->getOrder();
        $orderId = $order->getId();
        $this->_currentOrder = $order;
        $isEnabledSplitOrder = $this->isEnabledSplitOrder();
        if (!$isEnabledSplitOrder) {
            $this->createSellerOrdersByOrderId($orderId);
        }
    }

    /**
     * Create seller orders by order id
     * @param int $orderId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function createSellerOrdersByOrderId($orderId)
    {
        $objectManager = $this->getObjectManager();
        $orderData = $this->getOrderData($orderId);
        $customerId = $orderData->getCustomerId();
        $orderItems = $orderData->getAllItems();
        $storeId = $orderData->getStoreId();

        try {
            /** Execute auto create invoice - for some payment method as paypal express */
            $isInvoiced = $this->autoCreateInvoiceCommand->execute($orderData, $orderId);
            $isInvoiced = 0;
            $sellerData = [];
            //$calculate_parent_only = $this->helper->calculateCommissionForParent();

            /**
             * saving each order items
             */
            foreach ($orderItems as $item) {
                $productSku = $item->getSku();
                $productId = $objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($productSku);
                $product = $objectManager->get(\Magento\Catalog\Model\Product::class)->load($productId);
                $priceComparison = $this->enabledOfferProduct();
                if ($priceComparison) {
                    // @phpstan-ignore-next-line
                    $quote = $this->getOfferProduct($productId, $item->getQuoteItemId());
                    if ($quote) {
                        $sellerId = $quote->getSellerId();
                    } else {
                        $sellerId = $item->getLofSellerId() ?? null;
                    }
                } else {
                    $sellerId = $item->getLofSellerId() ?? null;
                }

                /** calculate commission and save order item */
                if ($this->helper->verifyCalculateCommission($item, $sellerId, $storeId)) {
                    $sellerDatas = $this->helper->getSellerById($sellerId);
                    $commission = $this->helper->getCommission($sellerId, $productId);
                    $productPrice = $item->getData('row_total') + $item->getData('tax_amount');
                    $discountAmount = $item->getDiscountAmount();
                    $priceCommission = $this->calculate->calculate($commission, $item);
                    $sellerCommission = $priceCommission;
                    $productQty = $item->getQtyOrdered();

                    /** saveSellerOrderItem */
                    $this->saveSellerOrderItem($item, $sellerId, $orderId, $orderData->getData('status'), $isInvoiced);
                    $country = $sellerDatas->getCountry();

                    $nationalShippingAmount = $product->getNationalShippingAmount();
                    $internationalShippingAmount = $product->getInternationalShippingAmount();

                    if ($orderData->getBillingAddress()->getCountryId() == $country) {
                        $sellerShippingAmount = $nationalShippingAmount * $productQty;
                    } else {
                        $sellerShippingAmount = $internationalShippingAmount * $productQty;
                    }
                    if (is_array($commission)) {
                        $sellerCommission = $commission['commission_amount'];
                    } else {
                        $sellerCommission = $commission;
                    }
                    if (array_key_exists($sellerId, $sellerData)) {
                        $sellerData[$sellerId]['price'] += $productPrice - $discountAmount;
                        $sellerData[$sellerId]['commission'] += (float)$sellerCommission;
                        $sellerData[$sellerId]['amount'] += $priceCommission;
                        $sellerData[$sellerId]['seller_id'] = $sellerId;
                        $sellerData[$sellerId]['shipping'] += $sellerShippingAmount;
                        $sellerData[$sellerId]['discount_amount'] += $discountAmount;
                    } else {
                        $sellerData[$sellerId]['price'] = $productPrice - $discountAmount;
                        $sellerData[$sellerId]['commission'] = (float)$sellerCommission;
                        $sellerData[$sellerId]['seller_id'] = $sellerId;
                        $sellerData[$sellerId]['amount'] = $priceCommission;
                        $sellerData[$sellerId]['shipping'] = $sellerShippingAmount;
                        $sellerData[$sellerId]['discount_amount'] = $discountAmount;
                    }
                }
            }
            /** Process update seller order data */
            $this->processUpdateSellerOrder($orderData, $sellerData, $orderId, $isInvoiced);
        } catch( \Exception $e) {
            //$this->messageManager->addError(__('Have error when update seller order %1.', $e->getMessage()));
        }
    }

    /**
     * Process update seller order
     *
     * @param mixed $orderData
     * @param mixed $sellerData
     * @param int $orderId
     * @param int $isInvoiced
     * @return void
     */
    public function processUpdateSellerOrder($orderData, $sellerData, $orderId, $isInvoiced)
    {
        if ($sellerData) {

            $objectManager = $this->getObjectManager();
            $customerOrderDetails = $objectManager->get(\Magento\Sales\Model\Order::class);
            $customerOrderData = $customerOrderDetails->load($orderId);

            $currencyCode = $orderData->getOrderCurrencyCode();
            $incrementId = $orderData->getIncrementId();
            $customerId = $orderData->getCustomerId();

            foreach ($sellerData as $sellerIds) {
                $sellerId = (int)$sellerIds ['seller_id'];

                try {
                    $sellerOrderModel = $objectManager->create(\Lof\MarketPlace\Model\Order::class);
                    $foundSellerOrder = $sellerOrderModel->getCollection()
                                        ->addFieldToFilter("seller_id", $sellerId)
                                        ->addFieldToFilter("order_id", (int)$orderId)
                                        ->getFirstItem();

                    if ($foundSellerOrder && $foundSellerOrder->getId()) {
                        $sellerOrderModel->load($foundSellerOrder->getId());
                        $sellerOrderModel
                                    ->setStatus($orderData->getData('status'))
                                    ->setIsInvoiced($isInvoiced);
                    } else {
                        $customerSession = $objectManager->get(\Magento\Customer\Model\Session::class);
                        if ($customerSession->isLoggedIn()) {
                            $customerId = $customerSession->getId();
                        }

                        $products = $objectManager->get(\Lof\MarketPlace\Model\Orderitems::class)->getCollection();
                        $products->addFieldToSelect('*');
                        $products->addFieldToFilter('order_id', $orderId);
                        $products->addFieldToFilter('seller_id', $sellerId);
                        $productIds = array_unique($products->getColumnValues('product_id'));

                        $orderShippingAmount = $customerOrderData->getShippingAmount();
                        $totalSellerShippingQty = $totalShippingQty = $shippingAmount = 0;
                        foreach ($orderData->getAllItems() as $item) {
                            $itemProductId = $item->getProductId();
                            if (in_array($itemProductId, $productIds) && $item->getIsVirtual() != 1) {
                                $totalSellerShippingQty = $totalSellerShippingQty + $item->getQtyOrdered();
                            }
                            if ($item->getIsVirtual() != 1) {
                                $totalShippingQty = $totalShippingQty + $item->getQtyOrdered();
                            }
                        }

                        if (!empty($orderShippingAmount) && !empty($totalSellerShippingQty) && !empty($totalShippingQty)) {
                            $shippingAmount = round($orderShippingAmount * ($totalSellerShippingQty / $totalShippingQty), 2);
                        }

                        $sellerOrderModel
                            ->setSellerId($sellerId)
                            ->setOrderId($orderId)
                            ->setSellerProductTotal($sellerIds['price'])
                            ->setCommission($sellerIds['commission'])
                            ->setSellerAmount($sellerIds['amount'])
                            ->setIncrementId($incrementId)
                            ->setOrderCurrencyCode($currencyCode)
                            ->setCustomerId($customerId)
                            ->setShippingAmount($shippingAmount)
                            ->setSellerShippingAmount(0)
                            ->setStatus($orderData->getData('status'))
                            ->setIsInvoiced($isInvoiced)
                            ->setDiscountAmount($sellerIds['discount_amount']);

                        if ($this->helper->getCommissionType() == CommissionType::TYPE_PRODUCT_SHIPPING) {
                            $shippingCommission = $this->helper->getShippingCommission($sellerId);
                            $sellerShippingAmount = $this->calculateShippingCommission
                                ->calculate($shippingCommission, $shippingAmount);
                            $sellerAmount = $sellerIds['amount'] + $sellerShippingAmount;
                            $sellerOrderModel->setSellerAmount($sellerAmount);
                            $sellerOrderModel->setSellerShippingAmount($sellerShippingAmount);
                        }
                    }
                    $sellerOrderModel->getResource()->save($sellerOrderModel);
                    //$sellerOrderModel->save();
                    // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
                } catch (\Exception $e) {
                    //
                }
                /**
                 * Send order details to seller
                 */
                $data = [];
                $sellerDataObj = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)
                    ->load($sellerId, 'seller_id');
                $data['email'] = $sellerDataObj->getData('email');
                $data['name'] = $sellerDataObj->getData('name');
                $data['order_id'] = $customerOrderData->getIncrementId();
                $data['order_status'] = $orderData->getData('status');
                if ($this->helper->getConfig('email_settings/enable_send_email')) {
                    $this->sender->newOrder($data);
                }
            }
        }
    }

    /**
     * save seller order item
     *
     * @param mixed $item
     * @param int $sellerId
     * @param int $orderId
     * @param string $orderStatus
     * @param int $isInvoiced
     * @return void
     */
    public function saveSellerOrderItem($item, $sellerId, $orderId, $orderStatus, $isInvoiced)
    {
        try {
            $objectManager = $this->getObjectManager();
            $sellerOrderItemsModel = $objectManager->create(\Lof\MarketPlace\Model\Orderitems::class);
            $productSku = $item->getSku();
            $productId = $objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($productSku);
            $itemId = $item->getItemId();

            $foundSellerOrderItem = $sellerOrderItemsModel->getCollection()
                            ->addFieldToFilter("seller_id", (int)$sellerId)
                            ->addFieldToFilter("order_id", (int)$orderId)
                            ->addFieldToFilter("product_id", (int)$productId)
                            ->addFieldToFilter("order_item_id", (int)$itemId)
                            ->getFirstItem();

            if ($foundSellerOrderItem && $foundSellerOrderItem->getId()) {
                $sellerOrderItemsModel->load($foundSellerOrderItem->getId());
                $sellerOrderItemsModel
                    ->setStatus($orderStatus)
                    ->setIsInvoiced($isInvoiced);
            } else {
                $customOptions = $item->getProductOptions();
                $customOptionArray = json_encode($customOptions);
                $commission = $this->helper->getCommission($sellerId, $productId);
                $productSku = $item->getSku();
                $productName = $item->getName();
                $productPrice = $item->getData('row_total') + $item->getData('tax_amount');
                $baseProductPrice = $item->getBasePrice();
                $priceCommission = $this->calculate->calculate($commission, $item);
                $sellerCommission = $priceCommission;
                $adminCommission = $item->getData('row_total')
                    + $item->getData('tax_amount')
                    - $item->getData('discount_amount')
                    - $priceCommission;
                $productQty = $item->getQtyOrdered();

                $sellerOrderItemsModel
                    ->setProductId($productId)
                    ->setProductSku($productSku)
                    ->setProductName($productName)
                    ->setSellerId($sellerId)
                    ->setOrderId($orderId)
                    ->setProductPrice($productPrice)
                    ->setBaseProductPrice($baseProductPrice)
                    ->setOrderItemId($itemId)
                    ->setProductQty($productQty)
                    ->setProductSku($productSku)
                    ->setProductName($productName)
                    ->setCommission($commission)
                    ->setOptions($customOptionArray)
                    ->setStatus($orderStatus)
                    ->setIsInvoiced($isInvoiced)
                    ->setSellerCommissionOrder($sellerCommission)
                    ->setAdminCommissionOrder($adminCommission);
            }
            $sellerOrderItemsModel->getResource()->save($sellerOrderItemsModel);
        } catch (\Exception $e) {
            //
        }
    }

    /**
     * Check is isEnabledSplitOrder module
     *
     * @return bool
     */
    public function isEnabledSplitOrder()
    {
        $isEnabledSplitOrder = $this->helper->isEnableModule('Lofmp_SplitOrder');
        if (!$isEnabledSplitOrder || !$this->helper->getConfig('module/enabled', null, 'lofmp_split_order')) {
            return false;
        }
        return true;
    }

    /**
     * Check is enabled offer product or not
     *
     * @return bool
     */
    public function enabledOfferProduct()
    {
        $enabled = $this->helper->isEnableModule('Lofmp_PriceComparison');
        return $enabled;
    }

    /**
     * Get offer product
     *
     * @param int $productId
     * @param int $itemId
     * @return mixed|object|null
     */
    public function getOfferProduct($productId, $itemId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $assignHelper = $objectManager->create(\Lofmp\PriceComparison\Helper\Data::class);
        $quote = $assignHelper->getQuoteCollection()
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('item_id', $itemId)
            ->getLastItem();
        return $quote && $quote->getId() ? $quote : null;
    }

    /**
     * Get current order
     *
     * @return mixed|null
     */
    public function getCurrentOrder()
    {
        return $this->_currentOrder;
    }

    /**
     * Get commission value
     *
     * @param float $commission
     * @param float $productPrice
     * @param int $productQty
     *
     * @return float
     */
    public function getCommissionValue($commissionProduct, $productPrice, $discountAmount)
    {
        $commission = 0;
        if (!is_array($commissionProduct)) {
            if ($commissionProduct != 0) {
                $commissionPerProduct = ($productPrice['row_total'] - $discountAmount) * ($commissionProduct / 100);
                $commission = $commissionPerProduct;
            } else {
                $commission = 0;
            }
            return $commission;
        }
        return $commission;
    }


    /**
     * Get order data by orderId
     *
     * @param int $orderId
     * @return Object
     */
    protected function getOrderData($orderId)
    {
        if (!isset($this->_orders[$orderId])) {
            $objectManager = $this->getObjectManager();
            $orderDetails = $objectManager->get(\Magento\Sales\Model\Order::class);
            $orderData = $orderDetails->load($orderId);
            $this->_orders[$orderId] = $orderData;
        }
        return $this->_orders[$orderId];
    }
}
