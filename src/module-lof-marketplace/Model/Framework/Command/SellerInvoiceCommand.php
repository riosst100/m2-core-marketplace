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

namespace Lof\MarketPlace\Model\Framework\Command;

use Magento\Sales\Model\Order;
use Magento\Framework\Message\ManagerInterface;
use Lof\MarketPlace\Model\Orderitems;
use Lof\MarketPlace\Model\ResourceModel\Orderitems as SellerOrderItemsResource;
use Lof\MarketPlace\Model\ResourceModel\Order as SellerOrderResource;
use Lof\MarketPlace\Model\ResourceModel\Invoice as SellerInvoiceResource;
use Lof\MarketPlace\Model\ResourceModel\Orderitems\CollectionFactory as CollectionFactory;

/**
 * OrderInvoice
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @see \Lof\MarketPlace\Observer\OrderInvoice
 */
class SellerInvoiceCommand implements SellerInvoiceCommandInterface
{
    const DEFAULT_INVOICE_STATE = "Pending";

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
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Model\AmountFactory
     */
    protected $amount;

    /**
     * @var \Lof\MarketPlace\Model\OrderFactory
     */
    protected $sellerOrderFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Lof\MarketPlace\Model\InvoiceFactory
     */
    protected $sellerInvoiceFactory;

    /**
     * @var \Lof\MarketPlace\Model\AmounttransactionFactory
     */
    protected $amountTransactionFactory;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var SellerOrderItemsResource
     */
    protected $sellerOrderItemsResource;

    /**
     * @var CollectionFactory
     */
    protected $orderItemsCollectionFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var SellerOrderResource
     */
    protected $sellerOrderResource;

    /**
     * @var SellerInvoiceResource
     */
    protected $sellerInvoiceResource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|null
     */
    protected $_dateTime = null;

    /**
     * Constructor
     *
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerProduct
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Model\AmountFactory $amount
     * @param \Lof\MarketPlace\Model\OrderFactory $sellerOrderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Lof\MarketPlace\Model\InvoiceFactory $sellerInvoiceFactory
     * @param \Lof\MarketPlace\Model\AmounttransactionFactory $amountTransactionFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param SellerOrderItemsResource $sellerOrderItemsResource
     * @param CollectionFactory $orderItemsCollectionFactory
     * @param ManagerInterface $messageManager
     * @param SellerOrderResource $sellerOrderResource
     * @param SellerInvoiceResource $sellerInvoiceResource
     * TODO: reduce params in construct, move some functions to other objects
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Model\AmountFactory $amount,
        \Lof\MarketPlace\Model\OrderFactory $sellerOrderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lof\MarketPlace\Model\InvoiceFactory $sellerInvoiceFactory,
        \Lof\MarketPlace\Model\AmounttransactionFactory $amountTransactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        SellerOrderItemsResource $sellerOrderItemsResource,
        CollectionFactory $orderItemsCollectionFactory,
        ManagerInterface $messageManager,
        SellerOrderResource $sellerOrderResource,
        SellerInvoiceResource $sellerInvoiceResource
    ) {
        $this->amount = $amount;
        $this->sender = $sender;
        $this->calculate = $calculate;
        $this->helper = $helper;
        $this->sellerProduct = $sellerProduct;
        $this->sellerOrderFactory = $sellerOrderFactory;
        $this->productFactory = $productFactory;
        $this->sellerInvoiceFactory = $sellerInvoiceFactory;
        $this->amountTransactionFactory = $amountTransactionFactory;
        $this->invoiceRepository = $invoiceRepository;
        $this->sellerOrderItemsResource = $sellerOrderItemsResource;
        $this->orderItemsCollectionFactory = $orderItemsCollectionFactory;
        $this->messageManager = $messageManager;
        $this->sellerOrderResource = $sellerOrderResource;
        $this->sellerInvoiceResource = $sellerInvoiceResource;
    }

    /**
     * get current date time
     *
     * @return string
     */
    public function getCurrentDateTime()
    {
        if (!$this->_dateTime) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_dateTime = $objectManager->get(\Magento\Framework\Stdlib\DateTime\DateTime::class);
        }
        return $this->_dateTime->gmtDate();
    }

    /**
     * @inheritdoc
     */
    public function execute($invoice, int $sellerId, $items = []): int
    {
        $order = $invoice->getOrder();
        $orderInvoice = $this->sellerInvoiceFactory->create();
        $orderSeller = $this->getSellerOrder($sellerId, $order->getId());
        /** Dont run when seller order is not exists */
        if (!$orderSeller) {
            return true;
        }
        $seller_amount = 0;
        $seller_commission = 0;
        $invoiceData = $this->initSellerInvoiceData($sellerId, $order->getId(), count($items), $invoice);
        try {
            /** Update seller order items */
            foreach ($items as $item) {
                $commission = $this->helper->getCommission($sellerId, $item->getProductId());
                $priceCommission = $this->calculate->calculate($commission, $item);

                $invoiceData['subtotal'] += (float)$item->getData('row_total');
                $invoiceData['base_subtotal'] += (float)$item->getData('base_row_total');
                $invoiceData['tax_amount'] += (float)$item->getData('tax_amount');
                $invoiceData['base_tax_amount'] += (float)$item->getData('base_tax_amount');
                $invoiceData['discount_amount'] += (float)$item->getData('discount_amount');
                $invoiceData['base_discount_amount'] += (float)$item->getData('base_discount_amount');
                $invoiceData['subtotal_incl_tax'] += (float)$item->getData('row_total_incl_tax');
                $invoiceData['base_subtotal_incl_tax'] += (float)$item->getData('base_row_total_incl_tax');

                $orderItemsCollection = $this->getSellerOrderItems($order->getId(), $sellerId, $item->getSku());

                foreach ($orderItemsCollection as $key => $orderItem) {
                    $seller_commission = $priceCommission + $orderItem->getData('seller_commission');
                    $seller_amount += $seller_commission;
                    $admin_commission = $item->getData('row_total')
                        + $item->getData('tax_amount')
                        - $item->getData('discount_amount')
                        - $priceCommission
                        + $orderItem->getData('admin_commission');
                    $qtyInvoiced = $orderItem->getData('qty_invoiced') + $item->getData('qty');

                    $orderItem->setQtyInvoiced($qtyInvoiced)
                                ->setAdminCommission($admin_commission)
                                ->setSellerCommission($seller_commission);

                    if ((int)$orderItem->getProductQty() >= $qtyInvoiced) {
                        /** Save Seller Order Item */
                        $orderItem = $this->saveSellerOrderItems($orderItem);
                    }
                }
            }

            /** Create Seller Invoice */
            $invoiceData['grand_total'] = (float)$invoiceData['subtotal']
                + (float)$invoiceData['shipping_amount']
                + (float)$invoiceData['tax_amount']
                - (float)$invoiceData['discount_amount'];

            $invoiceData['base_grand_total'] = (float)$invoiceData['base_subtotal']
                + (float)$invoiceData['base_shipping_amount']
                + (float)$invoiceData['base_tax_amount']
                - (float)$invoiceData['base_discount_amount'];

            $invoiceData['seller_amount'] = $seller_commission;
            $orderInvoiceId = 0;

            $invoiceDataObj = new \Magento\Framework\DataObject($invoiceData);
            $invoiceData = $invoiceDataObj->getData();
            $orderInvoice->setData($invoiceData);
            /** Save Seller New Invoice Data */
            $orderInvoiceId = $this->saveSellerInvoice($orderInvoice);

            $seller = $this->helper->getSellerById($sellerId);
            $total_sold = $seller->getTotalSold();
            if (count($seller->getData()) > 0) {
                /** Can ship order when create invoice or not */
                $sellerOrderItemsCollection = $this->getSellerOrderItems($order->getId(), $sellerId);
                $count_sale = 0;
                if (!$orderSeller->canShip($sellerId)) {
                    $orderSeller->setStatus(Order::STATE_COMPLETE);
                    foreach ($sellerOrderItemsCollection as $orderItem) {
                        $orderItem->setStatus(Orderitems::STATE_COMPLETED);
                        /** Update seller order item status */
                        $this->saveSellerOrderItems($orderItem);

                        $count_sale = $count_sale + (int)$orderItem->getQtyInvoiced();
                        $total_sold = $total_sold + (float)$orderItem->getSellerCommissionOrder();
                    }

                } else {
                    $orderSeller->setStatus(Order::STATE_PROCESSING);
                    foreach ($sellerOrderItemsCollection as $orderItem) {
                        $orderItem->setStatus(Orderitems::STATE_PROCESSING);
                        /** Update seller order item status */
                        $this->saveSellerOrderItems($orderItem);

                        $count_sale = $count_sale + (int)$orderItem->getQtyInvoiced();
                        $total_sold = $total_sold + (float)$orderItem->getSellerCommissionOrder();
                    }
                }
                /** Update seller total sale, total sold */
                if ($count_sale) {
                    $seller->setSale($count_sale)
                            ->setTotalSold($total_sold);
                    $seller->save();
                }

                if ($orderSeller->getStatus() == Order::STATE_COMPLETE) {
                    $description = __('Amount from order') . ' #'
                        . $order->getId() . ',' . __('invoice') . ' #' . $invoice->getId();
                    /** Update seller amount */
                    $this->updateSellerAmount($sellerId, $seller_amount, $description);
                }

                $orderSeller->setIsInvoiced($count_sale);
                /** Save seller Order Data */
                $orderSeller = $this->saveSellerOrder($orderSeller);

                /** Send new invoice notification email */
                $this->sendNewInvoiceEmail($seller, $invoice, $order->getIncrementId(), $order);
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Have error when create seller invoice.'));
            //$this->messageManager->addError(__('Have error when create seller invoice %1.', $e->getMessage()));
        }
        return $orderInvoiceId;
    }

    /**
     * Update seller amount
     *
     * @param int $updateSellerId
     * @param double|float|int $totalAmount
     * @param string $description
     *
     * @return void
     */
    public function updateSellerAmount($updateSellerId, $totalAmount, $description)
    {
        $amountTransactionModel = $this->amountTransactionFactory->create();
        $dateTime = $this->getCurrentDateTime();
        $sellerAmountModel = $this->amount->create()->load($updateSellerId, 'seller_id');
        $remainingAmount = $sellerAmountModel->getAmount();
        $totalRemainingAmount = $remainingAmount + $totalAmount;
        $amountTransactionModel
            ->setSellerId($updateSellerId)
            ->setBalance($totalRemainingAmount)
            ->setDescription($description)
            ->setUpdatedAt($dateTime);
        $sellerAmountModel->setSellerId($updateSellerId)->setAmount($totalRemainingAmount);
        $sellerAmountModel->save();
        $amountTransactionModel->save();
    }

    /**
     * Send new invoice notification email
     *
     * @param \Lof\MarketPlace\Model\Seller|mixed $seller
     * @param \Lof\MarketPlace\Model\Invoice|mixed $invoice
     * @param string $orderIncrementId
     * @param mixed|null $order
     * @return void
     */
    public function sendNewInvoiceEmail($seller, $invoice, $orderIncrementId, $order = null)
    {
        if ($this->helper->getConfig('email_settings/enable_send_email')) {
            $invoiceStates = $this->invoiceRepository->create()->getStates();
            $invoice_status = isset($invoiceStates[$invoice->getState()]) ? $invoiceStates[$invoice->getState()]->getText() : self::DEFAULT_INVOICE_STATE;

            $data = [];
            $data['email'] = $seller->getEmail();
            $data['name'] = $seller->getName();
            $data['order_id'] = $orderIncrementId;
            $data['invoice_id'] = $invoice->getIncrementId();
            $data['invoice_status'] = $invoice_status;
            $this->sender->newInvoice($data);
        }
    }

    /**
     * Init seller invoice object data
     *
     * @param int $sellerId
     * @param int $orderId
     * @param int|float $totalQty
     * @param mixed $invoice
     * @return mixed
     */
    public function initSellerInvoiceData(int $sellerId, int $orderId, $totalQty, $invoice)
    {
        $dateTime = $this->getCurrentDateTime();
        $invoiceData = [
            'seller_id' => $sellerId,
            'seller_order_id' => $orderId,
            'invoice_id' => $invoice->getId(),
            'state' => $invoice->getState(),
            'order_id' => $invoice->getOrderId(),
            'subtotal' => 0,
            'base_subtotal' => 0,
            'tax_amount' => 0,
            'base_tax_amount' => 0,
            'shipping_tax_amount' => 0,
            'base_shipping_tax_amount' => 0,
            'discount_amount' => 0,
            'base_discount_amount' => 0,
            'shipping_amount' => 0,
            'base_shipping_amount' => 0,
            'subtotal_incl_tax' => 0,
            'base_subtotal_incl_tax' => 0,
            'total_qty' => $totalQty,
            'updated_at' => $dateTime,
            'shipping_incl_tax' => 0,
            'base_shipping_incl_tax' => 0,
            'grand_total' => 0,
            'base_grand_total' => 0,
            'base_total_refunded' => 0,
        ];
        return $invoiceData;
    }

    /**
     * Get order by order id
     *
     * @param int $sellerId
     * @param int $orderId
     * @return \Lof\MarketPlace\Model\Order|mixed|object|null
     */
    protected function getSellerOrder($sellerId, $orderId)
    {
        $ordersellerCollection = $this->sellerOrderFactory->create()->getCollection();
        $ordersellerCollection
            ->addFieldToFilter("seller_id", $sellerId)
            ->addFieldToFilter("order_id", $orderId);
        $orderSellerData = $ordersellerCollection->getFirstItem();

        if ($orderSellerData && $orderSellerData->getId()) {
            $orderSeller = $this->sellerOrderFactory->create()
                ->load((int)$orderSellerData->getId());
        } else {
            $orderSeller = null;
        }
        return $orderSeller;
    }

    /**
     * get seller order items
     *
     * @param int $orderId
     * @param int $sellerId
     * @param string|null $sku
     * @return \Lof\MarketPlace\Model\ResourceModel\Orderitems\Collection|mixed|object
     */
    protected function getSellerOrderItems($orderId, $sellerId, $sku = null)
    {
        $collection = $this->orderItemsCollectionFactory->create()
                    ->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('seller_id', $sellerId);

        if ($sku) {
            $collection->addFieldToFilter('product_sku', $sku);
        }

        return $collection;
    }

    /**
     * save seller invoice
     *
     * @param \Lof\MarketPlace\Model\Invoice|mixed
     * @return int|null
     */
    protected function saveSellerInvoice($orderInvoice)
    {
        $this->sellerInvoiceResource->save($orderInvoice);
        return $orderInvoice->getId();
    }

    /**
     * save seller order items
     *
     * @param \Lof\MarketPlace\Model\Orderitems|mixed $orderItem
     * @return \Lof\MarketPlace\Model\Orderitems|mixed
     */
    protected function saveSellerOrderItems($orderItem)
    {
        $this->sellerOrderItemsResource->save($orderItem);
        return $orderItem;
    }

    /**
     * save seller order items
     *
     * @param \Lof\MarketPlace\Model\Order|mixed $orderSeller
     * @return \Lof\MarketPlace\Model\Order|mixed
     */
    protected function saveSellerOrder($orderSeller)
    {
        $this->sellerOrderResource->save($orderSeller);
        return $orderSeller;
    }
}
