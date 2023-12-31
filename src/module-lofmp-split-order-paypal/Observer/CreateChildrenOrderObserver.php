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
 * @package    Lofmp_SplitOrderPaypal
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrderPaypal\Observer;

use Lofmp\SplitOrder\Api\QuoteHandlerInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
use Lof\MarketPlace\Observer\OrderStatusChanged as MarketplaceOrderSubmitAfter;

class CreateChildrenOrderObserver implements ObserverInterface
{
    /**
     * @var string
     */
    protected $_defaultFakePayment = "marketplace_paypal"; //marketplace_paypal or checkmo

    /**
     * @var QuoteHandlerInterface
     */
    protected $quoteHandler;

    /**
     * @var CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var MarketplaceOrderSubmitAfter
     */
    protected $marketplaceOrderSubmitAfter;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @param Transaction $transaction
     * @param InvoiceSender $invoiceSender
     * @param Session $checkoutSession
     * @param QuoteHandlerInterface $quoteHandler
     * @param InvoiceService $invoiceService
     * @param MarketplaceOrderSubmitAfter $marketplaceOrderSubmitAfter
     * @param CartManagementInterface $quoteManagement
     * @param QuoteFactory $quoteFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        Transaction $transaction,
        InvoiceSender $invoiceSender,
        Session $checkoutSession,
        QuoteHandlerInterface $quoteHandler,
        InvoiceService $invoiceService,
        MarketplaceOrderSubmitAfter $marketplaceOrderSubmitAfter,
        CartManagementInterface $quoteManagement,
        QuoteFactory $quoteFactory,
        CartRepositoryInterface $quoteRepository,
        ManagerInterface $eventManager
    ) {
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->_checkoutSession = $checkoutSession;
        $this->quoteHandler = $quoteHandler;
        $this->marketplaceOrderSubmitAfter = $marketplaceOrderSubmitAfter;
        $this->quoteManagement = $quoteManagement;
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->_eventManager = $eventManager;
    }

    public function execute(Observer $observer)
    {
        $currentOrder = $observer->getOrder();
        if (!$currentOrder->getPpIsMainOrder()) {
            return $this;
        }

        $currentQuote = $observer->getQuote();
        $quotes = $this->quoteHandler->normalizeQuotes($currentQuote);

        // Do not split order if only Admin's products
        if (empty($quotes) || (count($quotes) == 1 && isset($quotes[0]['seller_id']) && $quotes[0]['seller_id'] == 0)) {
            return $this;
        }

//        if (count($quotes) == 1 && !isset($quotes[0]['seller_id'])) {
//            $this->marketplaceOrderSubmitAfter->createSellerOrdersByOrderId($currentOrder->getId());
//            return $this;
//        }

        // Collect list of data addresses.
        $addresses = $this->quoteHandler->collectAddressesData($currentQuote);
        $orderIds = [];
        foreach ($quotes as $sellerId => $quoteItem) {
            $items = $quoteItem['items'];
            $split = $this->quoteFactory->create();

            // Set all customer definition data.
            $this->quoteHandler->setCustomerData($currentQuote, $split);
            $this->quoteRepository->save($split);

            // Map quote items.
            foreach ($items as $item) {
                // Add item by item.
                $item->setId(null);
                $split->addItem($item);
            }
            $payment = null;
            $this->quoteHandler->populateQuote($quotes, $split, $items, $addresses, $payment, $sellerId);

            $split->setPaymentMethod($this->_defaultFakePayment); // fake payment method
            $split->setInventoryProcessed(false);

            // Set Sales Order Payment
            $split->getPayment()->importData(['method' => $this->_defaultFakePayment]);
            // Set order extra data into quote then save in sales_model_service_quote_submit_before
            $split->setPpParentOrderId($currentOrder->getId());
            $split->setPpIsMainOrder(0);
            $split->setPpOrderState($currentOrder->getState());
            $split->setPpOrderStatus($currentOrder->getStatus());
            $this->quoteRepository->save($split);

            // Dispatch event as Magento standard once per each quote split.
            $this->_eventManager->dispatch(
                'checkout_submit_before',
                ['quote' => $split]
            );
            $orderChild = $this->quoteManagement->submit($split);

            $split->setInventoryProcessed(false);
            $split->save($split);

            if (!$orderChild) {
                return $this;
            }


            if (null == $orderChild) {
                throw new LocalizedException(__('Please try to place the order again.'));
            } else {
                $this->_order = $orderChild;
                $orders[] = $orderChild;
                $orderIds[$orderChild->getId()] = $orderChild->getIncrementId();
                $this->marketplaceOrderSubmitAfter->createSellerOrdersByOrderId($orderChild->getId());

                $currentInvoice = current($currentOrder->getInvoiceCollection()->getItems());
                if ($currentInvoice) {
                    $invoiceChild = $this->invoiceService->prepareInvoice($orderChild);
                    $invoiceChild->register();
                    $invoiceChild->setState($currentInvoice->getState());
                    $invoiceChild->save();
//                $transactionSave = $this->transaction->addObject(
//                    $invoice
//                )->addObject(
//                    $invoice->getOrder()
//                );
//                $transactionSave->save();
                    $this->invoiceSender->send($invoiceChild);
                }
            }
            $this->_checkoutSession->setOrderIds($orderIds);
        }
        return $this;
    }
}
