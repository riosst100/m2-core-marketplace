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

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @see \Lof\MarketPlace\Observer\OrderStatusChanged
 */
class AutoCreateInvoiceCommand implements AutoCreateInvoiceCommandInterface
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\InvoiceOrderSaveAfter
     */
    protected $invoiceOrder;

    /**
     * @var \Lof\MarketPlace\Observer\OrderInvoice
     */
    protected $sellerOrderInvoice;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var mixed|null
     */
    protected $_objectManager = null;

    /**
     * @var bool
     */
    protected $_flag = false;

    /**
     * AutoCreateInvoiceCommand constructor.
     *
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\InvoiceOrderSaveAfter $invoiceOrder
     * @param \Lof\MarketPlace\Observer\OrderInvoice $sellerOrderInvoice
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\InvoiceOrderSaveAfter $invoiceOrder,
        \Lof\MarketPlace\Observer\OrderInvoice $sellerOrderInvoice,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->invoiceOrder = $invoiceOrder;
        $this->sellerOrderInvoice = $sellerOrderInvoice;
        $this->productRepository = $productRepository;
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
     * @inheritdoc
     */
    public function isAllowAutoGenerateInvoice($orderItems, $orderData): bool
    {
        $paymentActionsAuth = __('Authorization');
        $paypalPaymentAction = $this->helper->getConfigPaypal('paypal_express/payment_action');
        if (
            ($this->getFlag() && is_array($orderItems))
            || ( is_array($orderItems)
            && $this->invoiceOrder->allowAutoGenerateInvoice($orderData)
            && $paypalPaymentAction == $paymentActionsAuth
            )
        ) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function forceEnableAutoInvoice($flag = true)
    {
        $this->_flag = $flag;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFlag(): bool
    {
        return $this->_flag;
    }

    /**
     * @param mixed|object $orderData
     * @param int $orderId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute($orderData, $orderId = 0): int
    {
        $orderId = $orderId ? $orderId : $orderData->getId();
        $orderItems = $orderData->getAllItems();
        $objectManager = $this->getObjectManager();
        $isInvoiced = 0;
        try {
            if ($this->isAllowAutoGenerateInvoice($orderItems, $orderData)) {
                $isInvoiced = count($orderItems);
                $invoice = $this->invoiceOrder->createInvoice($orderId);
                if ($isInvoiced && $invoice) {
                    $sellerInvoice = [];
                    foreach ($invoice->getAllItems() as $item) {
                        $productId = $item->getProductId();
                        $sellerId = $item->getOrderItem()->getLofSellerId();
                        if ($sellerId) {
                            if (!isset($sellerInvoice[$sellerId])) {
                                $sellerInvoice[$sellerId] = [];
                            }
                            $sellerInvoice[$sellerId][] = $item;
                        }

                    }
                    if ($sellerInvoice) {
                        foreach ($sellerInvoice as $_sellerId => $items) {
                            $this->sellerOrderInvoice->createSellerInvoice($invoice, $items, $_sellerId, $objectManager);
                        }
                    }
                }
            } else {
                $isInvoiced = count($orderItems);
                $orderData
                    ->setTotalPaid($orderData->getGrandTotal())
                    ->setTotalInvoiced($orderData->getGrandTotal())
                    ->save();
            }
        } catch (\Exception $e) {
            /** $e->getMessage(); */
        }
        return $isInvoiced;
    }

    /**
     * Get seller product by product id
     *
     * @param int $productId
     * @return int
     */
    protected function getSellerProduct($productId)
    {
        $product = $this->productRepository->getById($productId);
        $sellerId = $product ? $product->getSellerId() : 0;

        return $sellerId;
    }
}
