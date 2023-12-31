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
use Lof\MarketPlace\Model\Framework\Command\SellerRefundCommandInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderRefund implements ObserverInterface
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
     * @var SellerRefundCommandInterface
     */
    protected $sellerRefundCommand;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * OrderRefund constructor.
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param SellerRefundCommandInterface $sellerRefundCommand
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        SellerRefundCommandInterface $sellerRefundCommand,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->sellerRefundCommand = $sellerRefundCommand;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();
        $orderItems = $order->getAllItems();
        $customerId = $order->getCustomerId();
        $sellerRefund = [];

        foreach ($creditmemo->getItems() as $item) {
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
                if (!isset($sellerRefund[$sellerId])) {
                    $sellerRefund[$sellerId] = [];
                }
                $sellerRefund[$sellerId][] = $item;
            }
        }

        if ($sellerRefund) {
            foreach ($sellerRefund as $sellerId => $items) {
                $this->sellerRefundCommand->execute($creditmemo, $sellerId, $items);
            }
        }
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

}
