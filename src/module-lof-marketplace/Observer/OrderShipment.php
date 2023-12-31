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
use Lof\MarketPlace\Model\Framework\Command\SellerShipmentCommandInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderShipment implements ObserverInterface
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var SellerShipmentCommandInterface
     */
    protected $sellerShipmentCommand;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * OrderShipment constructor.
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param SellerShipmentCommandInterface $sellerShipmentCommand
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        SellerShipmentCommandInterface $sellerShipmentCommand,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->sellerShipmentCommand = $sellerShipmentCommand;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $orderItems = $order->getAllItems();
        $customerId = $order->getCustomerId();
        $sellerShipment = [];

        foreach ($shipment->getAllItems() as $item) {
            $productId = $item->getProductId();

            $priceComparison = $this->enabledOfferProduct();
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
            if (!empty($sellerId)) {
                if (!isset($sellerShipment[$sellerId])) {
                    $sellerShipment[$sellerId] = [];
                }
                $sellerShipment[$sellerId][] = $item;
            }
        }

        if ($sellerShipment) { //check the invoice have products of seller or not
            foreach ($sellerShipment as $_sellerId => $items) {
                $this->sellerShipmentCommand->execute($order, $_sellerId, $items);
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
     * @param int $customerId
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
}
