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

namespace Lof\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\SellerorderInterface;
use Lof\MarketPlace\Api\SalesRepositoryInterface;
use Magento\Framework\Model\AbstractModel;
use Lof\MarketPlace\Helper\SellerOrderHelper;
use Lof\MarketPlace\Api\Data\SellerorderItemInterfaceFactory;
use Lof\MarketPlace\Api\Data\SellerorderItemInterface;
use Magento\Authorization\Model\CompositeUserContext;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order as BaseOrder;
use Magento\TestFramework\Exception\NoSuchActionException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Order extends AbstractModel implements SellerorderInterface, SalesRepositoryInterface
{
    /**
     * @var string
     */
    const STATE_CLOSED = 'closed';

    /**
     * @var string
     */
    const STATE_COMPLETED = 'completed';

    /**
     * @var SellerOrderHelper
     */
    protected $helper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CompositeUserContext
     */
    protected $userContext;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    private $orderItem;

    /**
     * @var \Lof\MarketPlace\Helper\Seller
     */
    private $helperSeller;

    /**
     * @var \Lof\MarketPlace\Model\RefundFactory
     */
    protected $refundFactory;

    /**
     * @var SellerorderItemInterfaceFactory
     */
    protected $sellerOrderItemFactory;

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_order';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'order';

    /**
     * Order constructor.
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param Context $context
     * @param Registry $registry
     * @param CompositeUserContext $userContext
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param SellerOrderHelper $helper
     * @param \Lof\MarketPlace\Helper\Seller $helperSeller
     * @param SellerFactory $sellerFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Lof\MarketPlace\Model\RefundFactory $refundFactory
     * @param SellerorderItemInterfaceFactory $sellerOrderItemFactory
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        Context $context,
        Registry $registry,
        CompositeUserContext $userContext,
        Orderitems $orderitems,
        SellerOrderHelper $helper,
        \Lof\MarketPlace\Helper\Seller $helperSeller,
        SellerFactory $sellerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Lof\MarketPlace\Model\RefundFactory $refundFactory,
        SellerorderItemInterfaceFactory $sellerOrderItemFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null
    ) {
        $this->helper = $helper;
        $this->orderItem = $orderitems;
        $this->priceCurrency = $priceCurrency;
        $this->sellerFactory = $sellerFactory;
        $this->orderFactory = $orderFactory;
        $this->refundFactory = $refundFactory;
        $this->userContext = $userContext;
        $this->helperSeller = $helperSeller;
        $this->sellerOrderItemFactory = $sellerOrderItemFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Order::class);
    }

    /**
     * @return BaseOrder
     */
    public function getOrder()
    {
        return $this->orderFactory->create()->load($this->getOrderId(), 'entity_id');
    }

    /**
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function getAllItems()
    {
        if ($this->getData('all_items') == null) {
            $items = [];
            foreach ($this->getOrder()->getAllItems() as $item) {
                if ($item->getOrderId() == $this->getOrderId()) {
                    $items[$item->getId()] = $item;
                }
            }
            $this->setData('all_items', $items);
        }
        return $this->getData('all_items');
    }

    /**
     * Retrieve order invoice availability
     * @param int|null $sellerId
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function canInvoice($sellerId = null)
    {
        $order = $this->getOrder();

        if ($this->canUnhold() || $order->isPaymentReview()) {
            return false;
        }

        $status = $this->getStatus();

        if ($this->isCanceled() || $status === BaseOrder::STATE_COMPLETE || $status === BaseOrder::STATE_CLOSED) {
            return false;
        }
        if (!$sellerId) {
            $sellerId = $this->helperSeller->getSellerId();
        }
        $orderItems = $this->orderItem->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('order_id', $this->getOrderId());
        foreach ($this->getAllItems() as $item) {
            foreach ($orderItems as $orderItem) {
                if ($item->getItemId() == $orderItem->getOrderItemId()
                    && $item->getQtyToInvoice() > 0
                    && !$item->getLockedDoInvoice()
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check whether order is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->getStatus() === BaseOrder::STATE_CANCELED;
    }

    /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        if ($this->getOrder()->isPaymentReview()) {
            return false;
        }
        return $this->getStatus() === BaseOrder::STATE_HOLDED;
    }

    /**
     * @return bool
     */
    public function canCancel()
    {
        if ($this->canUnhold()) {
            return false;
        }
        $allInvoiced = true;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()) {
                $allInvoiced = false;
                break;
            }
        }
        if ($allInvoiced) {
            return false;
        }
        $state = $this->getStatus();
        if ($this->isCanceled() || $state === BaseOrder::STATE_COMPLETE || $state === BaseOrder::STATE_CLOSED) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order shipment availability
     * @param int|null $sellerId
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function canShip($sellerId = null)
    {
        $order = $this->getOrder();

        if ($this->canUnhold() || $order->isPaymentReview()) {
            return false;
        }

        if ($order->getIsVirtual() || $order->isCanceled()) {
            return false;
        }
        if (!$sellerId) {
            $sellerId = $this->helperSeller->getSellerId();
        }
        $orderItems = $this->orderItem->getCollection()
            ->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToFilter('order_id', $this->getOrderId());
        foreach ($this->getAllItems() as $item) {
            foreach ($orderItems as $orderItem) {
                if ($item->getItemId() == $orderItem->getOrderItemId()
                    && $item->getQtyToShip() > 0
                    && !$item->getIsVirtual()
                    && !$item->getLockedDoShip()
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if ($this->hasForcedCanCreditmemo()) {
            return $this->getForcedCanCreditmemo();
        }

        if ($this->canUnhold() || $this->getOrder()->isPaymentReview()) {
            return false;
        }

        if ($this->isCanceled() || $this->getState() === BaseOrder::STATE_CLOSED) {
            return false;
        }

        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->priceCurrency->round($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001) {
            return false;
        }

        $total_refunded = $this->getSellerRefundedTotal($this->getOrderId(), $this->getSellerId());

        if ($total_refunded) {
            $total_order_refunded = $this->getTotalRefunded();
            $seller_order_total = $this->getSellerOrderSubTotal();
            if ($total_refunded >= $total_order_refunded || ($seller_order_total && $total_refunded >= $seller_order_total)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return float|null
     */
    public function getTotalPaid()
    {
        return $this->getOrder()->getTotalPaid();
    }

    /**
     * @return float|null
     */
    public function getTotalRefunded()
    {
        return $this->getOrder()->getTotalRefunded();
    }

    /**
     * @return float|null
     */
    public function getShippingRefunded()
    {
        return $this->getOrder()->getShippingRefunded();
    }

    /**
     * get seller order Sub total
     * @return float|int
     */
    public function getSellerOrderSubTotal()
    {
        return (float)$this->getSellerProductTotal() + (float)$this->getShippingAmount() - (float)$this->getDiscountAmount();
    }

    /**
     * @param int $orderId
     * @return int|null
     */
    public function getRefundId($orderId)
    {
        $refund = $this->refundFactory->create()->load($orderId, 'order_id');
        if ($refund) {
            return $refund->getRefundId();
        }
        return null;
    }

    /**
     * @param int $orderId
     * @param int $sellerId
     * @return mixed|array
     */
    public function getRefundBySeller($orderId, $sellerId)
    {
        $collection = $this->refundFactory->create()->getCollection()
                                        ->addFieldToFilter("order_id", $orderId)
                                        ->addFieldToFilter("seller_id", $sellerId)
                                        ->addFieldToFilter("status", Refund::STATUS_REFUNDED);
        if ($collection->count()) {
            return $collection;
        }
        return null;
    }

    /**
     * @param int $orderId
     * @param int $sellerId
     * @return float|int
     */
    public function getSellerRefundedTotal($orderId, $sellerId)
    {
        $collection = $this->getRefundBySeller($orderId, $sellerId);
        if ($collection) {
            $connection = $collection->getConnection();
            $countSelect = clone $collection->getSelect();
            $countSelect
                        ->reset(\Magento\Framework\DB\Select::COLUMNS)
                        ->columns(array('total_refunded' => 'SUM(refunded)'));
            return $connection->fetchOne($countSelect);
        }
        return 0;
    }

    /**
     * GET seller order
     * @param int $customerId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getSellerOrders($customerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('status', Seller::STATUS_ENABLED)
                        ->getFirstItem();
        $sellerId = $seller && $seller->getId() ? $seller->getId() : 0;
        if ($sellerId) {
            $res = [
                'code' => 405,
                'message' => __('Get data failed.')
            ];
            $data = $this->getCollection()->addFieldToFilter('seller_id', $sellerId)->getData();
            if ($data) {
                $res['code'] = 0;
                $res['message'] = __('Get data success!');
                $res['result']['order'][] = $data;
            } else {
                $res['code'] = 0;
                $res['message'] = __('Get data success!');
                $res['result']['order'] = [];
            }
        } else {
            throw new NoSuchEntityException(__('Customer has not register seller yet.'));
        }
        return $res;
    }

    /**
     * {@inheritdoc}
     * @throws NoSuchActionException
     */
    public function getSellerOrderById($orderId, $customerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('status', Seller::STATUS_ENABLED)
                        ->getFirstItem();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $res = ['code' => 405, 'message' => __('Get data failed.')];
        $order_data[] = $this->load($orderId)->getData();
        if ($seller && $seller->getId()) {
            $seller_id = $seller->getId();
        } else {
            throw new NoSuchEntityException(__('Customer has not registered the seller yet'));
        }

        if ($order_data) {
            try {
                $order = $objectManager->create(\Magento\Sales\Model\Order::class)
                    ->loadByIncrementId($order_data[0]["increment_id"]);

                $orderItems = $order->getAllItems();
                $product = [];
                foreach ($orderItems as $item) {
                    if ($item->getBaseRowTotal() > 0) {
                        $product[] = [
                            'entity_id' => (int)$item->getId(),
                            'quantity' => (int)$item->getQtyOrdered(),
                            'description' => $item->getDescription(),
                            'name' => $item->getName(),
                            'sku' => $item->getSku(),
                            'product_type' => $item->getProductType(),
                            'price' => (double)$item->getPrice()
                        ];
                    }
                }

                if ($seller_id == $order_data[0]["seller_id"]) {
                    $order_data[0]["product_list"] = $product;
                    $res['code'] = 0;
                    $res['message'] = __('Get data success!');
                    $res['result']['order'] = $order_data;
                }
                // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            } catch (\Exception $e) {
                //
            }
        }

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function orderCancel($OrderId, $customerId)
    {
        $seller = $this->sellerFactory->create()->getCollection()
                            ->addFieldToFilter('customer_id', $customerId)
                            ->addFieldToFilter('status', Seller::STATUS_ENABLED)
                            ->getFirstItem();
        if ($seller && $seller->getId()) {
            $seller_id = $seller->getId();
        } else {
            throw new NoSuchEntityException(__('Customer has not registered the seller yet'));
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create(\Lof\MarketPlace\Helper\Data::class);
        $order_controller = $objectManager->create(\Lof\MarketPlace\Controller\Marketplace\Order::class);
        $res = ['code' => 405, 'message' => __('Get data failed.')];

        if ($order = $order_controller->_initOrder($OrderId, $seller_id)) {
            $flag = $helper->cancelorder($order, $seller_id);
            if ($flag) {
                $trackingcoll = $this->getCollection()
                    ->addFieldToFilter('order_id', $OrderId)
                    ->addFieldToFilter('seller_id', $seller_id);
                foreach ($trackingcoll as $tracking) {
                    $tracking->setTrackingNumber('canceled');
                    $tracking->setCarrierName('canceled');
                    $tracking->setIsCanceled(1);
                    $tracking->setStatus('canceled');
                    $tracking->save();
                }

                $res = ['code' => 0, 'message' => __('Update data success!')];
            }
        }
        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function createSellerOrder(string $orderId)
    {
        $message = '';
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderCollection = $objectManager->create(\Magento\Sales\Model\ResourceModel\Order\Collection::class);
            $orderCollection->addFieldToFilter("increment_id", $orderId);
            $foundOrder = $orderCollection->getFirstItem();
            $orderId = $foundOrder && $foundOrder->getData("entity_id") ? (int)$foundOrder->getData("entity_id") : 0;

            if ($this->helper->createSellerOrder($orderId)) {
                $message = __('There orders of sellers were created completely.');
            } else {
                $message = __('Can not create seller orders.');
            }
        } catch (\Exception $e) {
            throw new NoSuchEntityException(__('Error: Can not create seller order.'));
        }
        return $message;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * @inheritDoc
     */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSellerId($sellerId)
    {
        return $this->setData(self::SELLER_ID, $sellerId);
    }

    /**
     * @inheritDoc
     */
    public function getCommission()
    {
        return $this->getData(self::COMMISSION);
    }

    /**
     * @inheritDoc
     */
    public function setCommission($commission = null)
    {
        return $this->setData(self::COMMISSION, $commission);
    }

    /**
     * @inheritDoc
     */
    public function getSellerProductTotal()
    {
        return $this->getData(self::SELLER_PRODUCT_TOTAL);
    }

    /**
     * @inheritDoc
     */
    public function setSellerProductTotal($sellerProductTotal = null)
    {
        return $this->setData(self::SELLER_PRODUCT_TOTAL, $sellerProductTotal);
    }

    /**
     * @inheritDoc
     */
    public function getSellerAmount()
    {
        return $this->getData(self::SELLER_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setSellerAmount($sellerAmount)
    {
        return $this->setData(self::SELLER_AMOUNT, $sellerAmount);
    }

    /**
     * @inheritDoc
     */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setDiscountAmount($discountAmount = null)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discountAmount);
    }

    /**
     * @inheritDoc
     */
    public function getIsInvoiced()
    {
        return $this->getData(self::IS_INVOICED);
    }

    /**
     * @inheritDoc
     */
    public function setIsInvoiced($isInvoiced)
    {
        return $this->setData(self::IS_INVOICED, $isInvoiced);
    }

    /**
     * @inheritDoc
     */
    public function getIsShipped()
    {
        return $this->getData(self::IS_SHIPPED);
    }

    /**
     * @inheritDoc
     */
    public function setIsShipped($isShipped)
    {
        return $this->setData(self::IS_SHIPPED, $isShipped);
    }

    /**
     * @inheritDoc
     */
    public function getIsRefunded()
    {
        return $this->getData(self::IS_REFUNDED);
    }

    /**
     * @inheritDoc
     */
    public function setIsRefunded($isRefunded)
    {
        return $this->setData(self::IS_REFUNDED, $isRefunded);
    }

    /**
     * @inheritDoc
     */
    public function getIsReturned()
    {
        return $this->getData(self::IS_RETURNED);
    }

    /**
     * @inheritDoc
     */
    public function setIsReturned($isReturned)
    {
        return $this->setData(self::IS_RETURNED, $isReturned);
    }

    /**
     * @inheritDoc
     */
    public function getIsCanceled()
    {
        return $this->getData(self::IS_CANCELED);
    }

    /**
     * @inheritDoc
     */
    public function setIsCanceled($isCanceled)
    {
        return $this->setData(self::IS_CANCELED, $isCanceled);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritDoc
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritDoc
     */
    public function getShippingAmount()
    {
        return $this->getData(self::SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setShippingAmount($shippingAmount)
    {
        return $this->setData(self::SHIPPING_AMOUNT, $shippingAmount);
    }

    /**
     * @inheritDoc
     */
    public function getSellerShippingAmount()
    {
        return $this->getData(self::SELLER_SHIPPING_AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setSellerShippingAmount($sellerShippingAmount)
    {
        return $this->setData(self::SELLER_SHIPPING_AMOUNT, $sellerShippingAmount);
    }

    /**
     * @inheritDoc
     */
    public function getOrderCurrencyCode()
    {
        return $this->getData(self::ORDER_CURRENCY_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderCurrencyCode($orderCurrencyCode)
    {
        return $this->setData(self::ORDER_CURRENCY_CODE, $orderCurrencyCode);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritDoc
     */
    public function getLineItems()
    {
        return $this->getData(self::LINE_ITEMS);
    }

    /**
     * @inheritDoc
     */
    public function setLineItems($line_items = null)
    {
        return $this->setData(self::LINE_ITEMS, $line_items);
    }
}
