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

namespace Lof\MarketPlace\Controller\Marketplace\Order;

use Lof\MarketPlace\Helper\Seller;
use Lof\MarketPlace\Model\OrderFactory as MarketPlaceOrderFactory;
use Lof\MarketPlace\Model\OrderitemsFactory;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Service\InvoiceServiceFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Invoice extends \Lof\MarketPlace\Controller\Marketplace\Order
{
    /**
     * @var
     */
    protected $helper;

    /**
     * @var Seller
     */
    protected $sellerHelper;

    /**
     * @var MarketPlaceOrderFactory
     */
    protected $marketplaceOrderFactory;

    /**
     * @var OrderitemsFactory
     */
    protected $orderitemsFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var InvoiceServiceFactory
     */
    protected $invoiceServiceFactory;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * Invoice constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param InvoiceSender $invoiceSender
     * @param ShipmentSender $shipmentSender
     * @param ShipmentFactory $shipmentFactory
     * @param CreditmemoSender $creditmemoSender
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param CreditmemoFactory $creditmemoFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param StockConfigurationInterface $stockConfiguration
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderManagementInterface $orderManagement
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param Seller $sellerHelper
     * @param OrderitemsFactory $orderitemsFactory
     * @param OrderFactory $orderFactory
     * @param InvoiceServiceFactory $invoiceServiceFactory
     * @param MarketPlaceOrderFactory $marketplaceOrderFactory
     * @param \Magento\Framework\DB\Transaction $transaction
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        InvoiceSender $invoiceSender,
        ShipmentSender $shipmentSender,
        ShipmentFactory $shipmentFactory,
        CreditmemoSender $creditmemoSender,
        CreditmemoRepositoryInterface $creditmemoRepository,
        CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        StockConfigurationInterface $stockConfiguration,
        OrderRepositoryInterface $orderRepository,
        OrderManagementInterface $orderManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Helper\Data $helper,
        Seller $sellerHelper,
        OrderitemsFactory $orderitemsFactory,
        OrderFactory $orderFactory,
        InvoiceServiceFactory $invoiceServiceFactory,
        MarketPlaceOrderFactory $marketplaceOrderFactory,
        \Magento\Framework\DB\Transaction $transaction
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $invoiceSender,
            $shipmentSender,
            $shipmentFactory,
            $creditmemoSender,
            $creditmemoRepository,
            $creditmemoFactory,
            $invoiceRepository,
            $stockConfiguration,
            $orderRepository,
            $orderManagement,
            $coreRegistry,
            $customerSession,
            $helper
        );

        $this->sellerHelper = $sellerHelper;
        $this->marketplaceOrderFactory = $marketplaceOrderFactory;
        $this->orderitemsFactory = $orderitemsFactory;
        $this->orderFactory = $orderFactory;
        $this->invoiceServiceFactory = $invoiceServiceFactory;
        $this->transaction = $transaction;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function execute()
    {
        if ($order = $this->_initOrder()) {
            try {
                $sellerId = $this->sellerHelper->getSellerId();
                $this->createInvoice($order, $sellerId);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t create invoice for order right now.')
                );
            }

            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/orderview/view',
                [
                    'id' => $order->getEntityId(),
                    '_secure' => $this->getRequest()->isSecure(),
                ]
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/order',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param int $sellerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoice($order, $sellerId)
    {
        $orderId = $order->getId();
        $this->_eventManager->dispatch(
            'marketplace_seller_start_invoice',
            ['account_controller' => $this, 'order' => $order, 'seller_id' => $sellerId, 'order_id' => $orderId]
        );

        if ($order->canUnhold()) {
            $this->messageManager->addErrorMessage(
                __('Can not create invoice as order is in HOLD state')
            );
        } else {
            $data = [];
            $data['send_email'] = 1;
            $items = [];
            $shippingAmount = 0;
            $couponAmount = 0;
            $codcharges = 0;
            $tax = 0;

            $trackingsdata = $this->marketplaceOrderFactory->create()
                ->getCollection()
                ->addFieldToFilter(
                    'order_id',
                    $orderId
                )->addFieldToFilter(
                    'seller_id',
                    $sellerId
                );
            $sellerOrder = $trackingsdata->getFirstItem();
            foreach ($trackingsdata as $tracking) {
                $shippingAmount = $tracking->getShippingAmount();
                $couponAmount = $tracking->getCouponAmount();
            }

            $collection = $this->orderitemsFactory->create()
                ->getCollection()
                ->addFieldToFilter(
                    'order_id',
                    ['eq' => $orderId]
                )
                ->addFieldToFilter(
                    'seller_id',
                    ['eq' => $sellerId]
                );

            $row_total = 0;
            $row_base_total = 0;
            $row_tax_total = 0;
            $row_discount_total = 0;
            foreach ($collection as $saleproduct) {
                $orderData = $this->orderFactory->create()->load($orderId);
                $orderItems = $orderData->getAllItems();
                foreach ($orderItems as $item) {
                    if ($item->getData('item_id') == $saleproduct->getData('order_item_id')) {
                        $tax = $tax + $item->getData('tax_amount');
                        $items[$saleproduct->getData('order_item_id')] = $saleproduct->getProductQty();
                        $row_total += $item->getRowTotal();
                        $row_base_total += $item->getBaseRowTotal();
                        $row_discount_total += $item->getDiscountAmount();
                        $row_tax_total += $item->getTaxAmount();
                    }
                }
            }

            $itemsarray = $this->_getItemQtys($order, $items);
            if (count($itemsarray) > 0 && $sellerOrder->canInvoice($sellerId)) {
                $invoice = $this->invoiceServiceFactory->create()
                    ->prepareInvoice($order, $items);
                if (!$invoice) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('We can\'t save the invoice right now.')
                    );
                }
                if (!$invoice->getTotalQty()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('You can\'t create an invoice without products.')
                    );
                }

                $this->_coreRegistry->register(
                    'current_invoice',
                    $invoice
                );

                if (!empty($data['capture_case'])) {
                    $invoice->setRequestedCaptureCase(
                        $data['capture_case']
                    );
                }

                if (!empty($data['comment_text'])) {
                    $invoice->addComment(
                        $data['comment_text'],
                        isset($data['comment_customer_notify']),
                        isset($data['is_visible_on_front'])
                    );

                    $invoice->setCustomerNote($data['comment_text']);
                    $invoice->setCustomerNoteNotify(
                        isset($data['comment_customer_notify'])
                    );
                }

                if (!isset($itemsarray['subtotal'])
                    || (isset($itemsarray['subtotal']) && !$itemsarray['subtotal'])
                ) {
                    $itemsarray['subtotal'] = (float)$row_total;
                }
                if (!isset($itemsarray['baseSubtotal'])
                    || (isset($itemsarray['baseSubtotal']) && !$itemsarray['baseSubtotal'])
                ) {
                    $itemsarray['baseSubtotal'] = (float)$row_base_total;
                }
                $invoice->setBaseDiscountAmount($couponAmount);
                $invoice->setDiscountAmount($couponAmount);
                $invoice->setShippingAmount($shippingAmount);
                $invoice->setBaseShippingInclTax($shippingAmount);
                $invoice->setBaseShippingAmount($shippingAmount);
                $invoice->setSubtotal($itemsarray['subtotal']);
                $invoice->setBaseSubtotal($itemsarray['baseSubtotal']);

                $invoice->setGrandTotal(
                    $itemsarray['subtotal'] +
                    $shippingAmount +
                    $codcharges +
                    $tax -
                    $couponAmount
                );
                $invoice->setBaseGrandTotal(
                    $itemsarray['subtotal'] +
                    $shippingAmount +
                    $codcharges +
                    $tax -
                    $couponAmount
                );
                $invoice->register();

                $invoice->getOrder()->setCustomerNoteNotify(
                    !empty($data['send_email'])
                );
                $invoice->getOrder()->setIsInProcess(true);

                $transactionSave = $this->transaction->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $transactionSave->save();

                $this->_invoiceSender->send($invoice);

                $this->_eventManager->dispatch(
                    'marketplace_seller_complete_invoice',
                    ['account_controller' => $this, 'order' => $order, 'seller_id' => $sellerId, 'order_id' => $orderId, 'invoice' => $invoice]
                );

                $this->messageManager->addSuccessMessage(
                    __('Invoice has been created for this order.')
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __('Cannot create Invoice for this order.')
                );
            }
        }
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!!$this->helper->getConfig('sales_settings/can_invoice')) {
            return parent::dispatch($request);
        }

        $this->messageManager->addErrorMessage(
            __('We can\'t create invoice for order right now.')
        );

        return  $this->resultRedirectFactory->create()->setPath(
            'catalog/sales/orderview/view',
            [
                'id' => $this->getRequest()->getParam('id'),
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }
}
