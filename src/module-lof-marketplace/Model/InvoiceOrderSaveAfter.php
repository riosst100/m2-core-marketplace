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

class InvoiceOrderSaveAfter
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $_invoiceCollectionFactory;

    /**
     * @var \Magento\Sales\Api\InvoiceRepositoryInterface
     */
    protected $_invoiceRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var string[]
     */
    protected $_availablePayments = [
        'pp_parent_order_id'
    ];

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->_invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->_invoiceService = $invoiceService;
        $this->_transactionFactory = $transactionFactory;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_orderRepository = $orderRepository;
    }

    /**
     * @param int $orderId
     * @return \Magento\Sales\Api\Data\InvoiceInterface|\Magento\Sales\Model\Order\Invoice|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createInvoice($orderId)
    {
        try {
            $order = $this->_orderRepository->get($orderId);
            if ($order) {
                $invoices = $this->_invoiceCollectionFactory->create()
                    ->addAttributeToFilter('order_id', array('eq' => $order->getId()));

                $invoices->getSelect()->limit(1);

                if ((int)$invoices->count() !== 0) {
                    $invoices = $invoices->getFirstItem();
                    $invoice = $this->_invoiceRepository->get($invoices->getId());
                    return $invoice;
                }

                if (!$order->canInvoice()) {
                    return null;
                }

                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                $invoice->register();
                $invoice->getOrder()->setCustomerNoteNotify(false);
                $invoice->getOrder()->setIsInProcess(true);
                $order->addStatusHistoryComment(__('Automatically INVOICED'), false);
                $transactionSave = $this->_transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();

                return $invoice;
            }
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }

    /**
     * allowAutoGenerateInvoice
     *
     * @param mixed|object|array $orderData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function allowAutoGenerateInvoice($orderData)
    {
        $flag = false;
        $flag = $this->checkAvailablePayment($orderData);

        return $flag;
    }

    /**
     * get available payment
     * @return array|string[]
     */
    public function getAvailablePayment()
    {
        return $this->_availablePayments;
    }

    /**
     * set available payment
     * @param sring $payment_field_name
     * @return $this
     */
    public function setAvailablePayment($payment_field_name)
    {
        if (!in_array($payment_field_name, $this->_availablePayments)) {
            $this->_availablePayments[] = $payment_field_name;
        }
        return $this;
    }

    /**
     * checkAvailablePayment
     *
     * @param mixed|object|array $orderData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkAvailablePayment($orderData)
    {
        $flag = false;
        $availablePayments = $this->getAvailablePayment();
        foreach ($availablePayments as $_payment_field) {
            if ($orderData->getData($_payment_field) != 0) {
                $flag = true;
                break;
            }
        }
        return $flag;
    }
}
