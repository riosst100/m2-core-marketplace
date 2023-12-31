<?php

namespace Lofmp\SplitOrderPaypal\Plugin\Sales\OrderManagement;

use Lofmp\SplitOrder\Api\QuoteHandlerInterface;
use Magento\InventoryCatalogApi\Model\GetProductTypesBySkusInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\Paypal\Model\Config;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory;
use Magento\InventoryCatalogApi\Model\GetSkusByProductIdsInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionInterface;
use Magento\InventorySales\Model\CheckItemsQuantity;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;

/**
 * Add reservation during order placement
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AppendReservationsAfterOrderPlacementPlugin extends \Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin
{
    /**
     * @var PlaceReservationsForSalesEventInterface
     */
    private $placeReservationsForSalesEvent;

    /**
     * @var GetSkusByProductIdsInterface
     */
    private $getSkusByProductIds;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var SalesChannelInterfaceFactory
     */
    private $salesChannelFactory;

    /**
     * @var SalesEventInterfaceFactory
     */
    private $salesEventFactory;

    /**
     * @var ItemToSellInterfaceFactory
     */
    private $itemsToSellFactory;

    /**
     * @var CheckItemsQuantity
     */
    private $checkItemsQuantity;

    /**
     * @var StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * @var GetProductTypesBySkusInterface
     */
    private $getProductTypesBySkus;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var SalesEventExtensionFactory;
     */
    private $salesEventExtensionFactory;

    /**
     * @var QuoteHandlerInterface
     */
    protected $quoteHandler;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Config
     */
    protected $paypalConfig;

    /**
     * @param QuoteHandlerInterface $quoteHandler
     * @param QuoteFactory $quoteFactory
     * @param Config $paypalConfig
     * @param PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent
     * @param GetSkusByProductIdsInterface $getSkusByProductIds
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param SalesChannelInterfaceFactory $salesChannelFactory
     * @param SalesEventInterfaceFactory $salesEventFactory
     * @param ItemToSellInterfaceFactory $itemsToSellFactory
     * @param CheckItemsQuantity $checkItemsQuantity
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param GetProductTypesBySkusInterface $getProductTypesBySkus
     * @param IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType
     * @param SalesEventExtensionFactory $salesEventExtensionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        QuoteHandlerInterface $quoteHandler,
        QuoteFactory $quoteFactory,
        Config $paypalConfig,
        PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        GetSkusByProductIdsInterface $getSkusByProductIds,
        WebsiteRepositoryInterface $websiteRepository,
        SalesChannelInterfaceFactory $salesChannelFactory,
        SalesEventInterfaceFactory $salesEventFactory,
        ItemToSellInterfaceFactory $itemsToSellFactory,
        CheckItemsQuantity $checkItemsQuantity,
        StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        GetProductTypesBySkusInterface $getProductTypesBySkus,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        SalesEventExtensionFactory $salesEventExtensionFactory
    ) {
        $this->paypalConfig = $paypalConfig;
        $this->quoteFactory = $quoteFactory;
        $this->quoteHandler = $quoteHandler;
        $this->placeReservationsForSalesEvent = $placeReservationsForSalesEvent;
        $this->getSkusByProductIds = $getSkusByProductIds;
        $this->websiteRepository = $websiteRepository;
        $this->salesChannelFactory = $salesChannelFactory;
        $this->salesEventFactory = $salesEventFactory;
        $this->itemsToSellFactory = $itemsToSellFactory;
        $this->checkItemsQuantity = $checkItemsQuantity;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->getProductTypesBySkus = $getProductTypesBySkus;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->salesEventExtensionFactory = $salesEventExtensionFactory;
    }

    /**
     * Add reservation before place order
     *
     * In case of error during order placement exception add compensation
     *
     * @param OrderManagementInterface $subject
     * @param callable $proceed
     * @param OrderInterface $order
     * @return OrderInterface
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundPlace(
        OrderManagementInterface $subject,
        callable $proceed,
        OrderInterface $order
    ): OrderInterface {
        $itemsById = $itemsBySku = $itemsToSell = [];
        foreach ($order->getItems() as $item) {
            if (!isset($itemsById[$item->getProductId()])) {
                $itemsById[$item->getProductId()] = 0;
            }
            $itemsById[$item->getProductId()] += $item->getQtyOrdered();
        }
        $productSkus = $this->getSkusByProductIds->execute(array_keys($itemsById));
        $productTypes = $this->getProductTypesBySkus->execute($productSkus);

        foreach ($productSkus as $productId => $sku) {
            if (false === $this->isSourceItemManagementAllowedForProductType->execute($productTypes[$sku])) {
                continue;
            }

            $itemsBySku[$sku] = (float)$itemsById[$productId];
            $itemsToSell[] = $this->itemsToSellFactory->create([
                'sku' => $sku,
                'qty' => -(float)$itemsById[$productId]
            ]);
        }

        $websiteId = (int)$order->getStore()->getWebsiteId();
        $websiteCode = $this->websiteRepository->getById($websiteId)->getCode();
        $stockId = (int)$this->stockByWebsiteIdResolver->execute((int)$websiteId)->getStockId();

        $this->checkItemsQuantity->execute($itemsBySku, $stockId);

        /** @var SalesEventExtensionInterface */
        $salesEventExtension = $this->salesEventExtensionFactory->create([
            'data' => ['objectIncrementId' => (string)$order->getIncrementId()]
        ]);

        /** @var SalesEventInterface $salesEvent */
        $salesEvent = $this->salesEventFactory->create([
            'type' => SalesEventInterface::EVENT_ORDER_PLACED,
            'objectType' => SalesEventInterface::OBJECT_TYPE_ORDER,
            'objectId' => (string)$order->getEntityId()
        ]);
        $salesEvent->setExtensionAttributes($salesEventExtension);
        $salesChannel = $this->salesChannelFactory->create([
            'data' => [
                'type' => SalesChannelInterface::TYPE_WEBSITE,
                'code' => $websiteCode
            ]
        ]);

        if (!$this->getPpIsMainOrder($order)) {
            $this->placeReservationsForSalesEvent->execute($itemsToSell, $salesChannel, $salesEvent);
        }

        try {
            $order = $proceed($order);
        } catch (\Exception $e) {
            //add compensation
            foreach ($itemsToSell as $item) {
                $item->setQuantity(-(float)$item->getQuantity());
            }

            /** @var SalesEventInterface $salesEvent */
            $salesEvent = $this->salesEventFactory->create([
                'type' => SalesEventInterface::EVENT_ORDER_PLACE_FAILED,
                'objectType' => SalesEventInterface::OBJECT_TYPE_ORDER,
                'objectId' => (string)$order->getEntityId()
            ]);
            $salesEvent->setExtensionAttributes($salesEventExtension);

            $this->placeReservationsForSalesEvent->execute($itemsToSell, $salesChannel, $salesEvent);

            throw $e;
        }
        return $order;
    }

    /**
     * @param OrderInterface $order
     * @return bool
     */
    public function getPpIsMainOrder(OrderInterface $order)
    {
        // Do not split order if only Admin's products
        if (!$this->paypalConfig->isMethodActive($order->getPayment()->getMethod())) {
            return false;
        }

        $currentQuote = $this->quoteFactory->create()->load($order->getQuoteId());
        $quotes = $this->quoteHandler->normalizeQuotes($currentQuote);

        if (empty($quotes) || (count($quotes) == 1 && isset($quotes[0]['seller_id']) && $quotes[0]['seller_id'] == 0)) {
            return false;
        }

//        if (count($quotes) == 1 && !isset($quotes[0]['seller_id'])) {
//            return;
//        }

        return true;
    }
}
