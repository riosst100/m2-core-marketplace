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
use Lof\MarketPlace\Model\Framework\Command\SellerInvoiceCommandInterface;
use Lof\MarketPlace\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * OrderInvoice
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderInvoice implements ObserverInterface
{
    const DEFAULT_INVOICE_STATE = "Pending";

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var SellerInvoiceCommandInterface
     */
    protected $sellerInvoiceCommand;

    /**
     * @var OrderCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var bool
     */
    protected $_flag = true;

    /**
     * Constructor
     *
     * @param \Magento\GoogleAdwords\Helper\Data $helper
     * @param SellerInvoiceCommandInterface $sellerInvoiceCommand
     * @param OrderCollectionFactory $collectionFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        SellerInvoiceCommandInterface $sellerInvoiceCommand,
        OrderCollectionFactory $collectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->sellerInvoiceCommand = $sellerInvoiceCommand;
        $this->collectionFactory = $collectionFactory;
        $this->productRepository = $productRepository;
    }


    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $invoice->getOrder();

        if (!$this->checkAllowCreateInvoice($order)) {
            return $this;
        }

        $sellerInvoice = [];
        $customerId = $order->getCustomerId();
        $orderItems = $order->getAllItems();

        foreach ($invoice->getAllItems() as $item) {
            $productId = $item->getProductId();
            $priceComparison = $this->enabledOfferProduct();
            $sellerId = 0;
            if ($priceComparison) {
                // @phpstan-ignore-next-line
                $quoteItemId = $this->getQuoteItemId($item->getOrderItemId(), $orderItems);
                $quote = $this->getOfferProduct($productId, $quoteItemId);
                if ($quote) {
                    $sellerId = $quote->getSellerId();
                } else {
                    $sellerId = $item->getOrderItem()->getLofSellerId() ?? null;
                }
            } else {
                $sellerId = $item->getOrderItem()->getLofSellerId() ?? null;
            }
            if ($sellerId) {
                if (!isset($sellerInvoice[$sellerId])) {
                    $sellerInvoice[$sellerId] = [];
                }
                $sellerInvoice[$sellerId][] = $item; // collect all product items in the order for each seller
            }
        }

        if ($sellerInvoice) { //check the invoice have products of seller or not
            $currentSellerId = $this->helper->getSellerId();//get current logged in seller account on frontend.
            foreach ($sellerInvoice as $_sellerId => $items) {
                if ($currentSellerId) { //if is current seller is logged in, will create invoice of him only
                    $this->createSellerInvoice($invoice, $items, $_sellerId, $objectManager);
                    if ($_sellerId != $currentSellerId) {
                        break;
                    }
                } else {//else will genereate all invoice of all seller on order
                    $this->createSellerInvoice($invoice, $items, $_sellerId, $objectManager);
                }
            }
        }

        return $this;
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
     * @param $orderItemId
     * @param $orderItems
     * @return int
     */
    protected function getQuoteItemId($orderItemId, $orderItems)
    {
        $quoteItemId = 0;
        foreach ($orderItems as $_item) {
            if ($_item->getId() == $orderItemId) {
                $quoteItemId = $_item->getQuoteItemId();
                break;
            }
        }
        return $quoteItemId;
    }

    /**
     * check allow create invoice
     *
     * @param mixed $order
     * @return bool
     */
    public function checkAllowCreateInvoice($order)
    {
        $splitOrderEnabled = $this->helper->isEnableModule('Lofmp_SplitOrder');
        $sellerOrder = $this->getSellerOrderByOrderId($order->getId());

        if (
            !$this->_flag
            || ( $splitOrderEnabled
                && $this->helper->getConfig('module/enabled', null, 'lofmp_split_order')
                && !$sellerOrder
            )
        ) {
            return false;
        }
        return true;
    }

    /**
     * Get order by order id
     *
     * @param int $order_id
     * @return \Lof\MarketPlace\Model\Order|mixed|object|null
     */
    protected function getSellerOrderByOrderId($order_id)
    {
        $collection = $this->collectionFactory->create()
                        ->addFieldToFilter('order_id', $order_id);

        $foundItem = $collection->getFirstItem();
        return $foundItem && $foundItem->getId() ? $foundItem : null;
    }

    /**
     * @param mixed $invoice
     * @param mixed $items
     * @param int $sellerId
     * @param mixed $objectManager
     * @return int
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function createSellerInvoice($invoice, $items, $sellerId, $objectManager)
    {
        return $this->sellerInvoiceCommand->execute($invoice, (int)$sellerId, $items);
    }

    /**
     * Set flag allow create invoice or not
     *
     * @param bool $flag
     * @return bool
     */
    public function setFlag($flag = true)
    {
        $this->_flag = $flag;
        return $this;
    }
}
