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

namespace Lof\MarketPlace\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;
use Lof\MarketPlace\Model\Source\CommissionType;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderSaveAfter implements ObserverInterface
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
     * @var \Lof\MarketPlace\Model\CalculateShippingCommission
     */
    protected $calculateShippingCommission;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * OrderSaveAfter constructor.
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param \Lof\MarketPlace\Model\SellerProduct $sellerProduct
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param QuoteFactory $quoteFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\CalculateShippingCommission $calculateShippingCommission,
        \Lof\MarketPlace\Model\SellerProduct $sellerProduct,
        \Lof\MarketPlace\Model\Sender $sender,
        QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->sender = $sender;
        $this->calculate = $calculate;
        $this->calculateShippingCommission = $calculateShippingCommission;
        $this->helper = $helper;
        $this->sellerProduct = $sellerProduct;
        $this->quoteFactory = $quoteFactory;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $quoteModel = $this->quoteFactory->create()->load($order->getQuoteId());
        $orderId = $order->getId();
        $storeId = $order->getStoreId();
        $model = $observer->getEvent()->getDataObject();
        $isReorder = $quoteModel->getData("isReorder");
        $orderByAmin = $quoteModel->getData("order_by_admin");
        if (!$model || (!$model->isObjectNew() && !$isReorder && !$orderByAmin)) {
            return $this;
        }
        if ($isReorder || $orderByAmin) {
            try {
                $quoteModel->setData("isReorder", 0);
                $quoteModel->setData("order_by_admin", 0);
                $quoteModel->getResource()->save($quoteModel);
            } catch (\Exception $e) {
                //echo $e->getMessage();
            }
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderDetails = $objectManager->get(\Magento\Sales\Model\Order::class);
        $orderData = $orderDetails->load($orderId);
        $currencyCode = $orderData->getOrderCurrencyCode();
        $incrementId = $orderDetails->getIncrementId();
        $customerId = $orderDetails->getCustomerId();
        if ($isReorder || $orderByAmin) {
            $orderItems = $quoteModel->getAllItems();
            $countryName = $quoteModel->getBillingAddress()->getCountryId();
        } else {
            $orderItems = $orderData->getAllItems();
            $countryName = $orderData->getBillingAddress()->getCountryId();
        }
        $sellerData = [];

        //$calculate_parent_only = $this->helper->calculateCommissionForParent();

        foreach ($orderItems as $item) {
            $productId = $item->getProductId();
            $itemId = $item->getItemId();
            $customOptions = $item->getProductOptions();
            $customOptionArray = json_encode($customOptions);
            $product = $objectManager->get(\Magento\Catalog\Model\Product::class)->load($productId);

            $priceComparison = $this->helper->isEnableModule('Lofmp_PriceComparison');
            if ($priceComparison) {
                // @phpstan-ignore-next-line
                $assignHelper = $objectManager->create(\Lofmp\PriceComparison\Helper\Data::class);
                $quote = $assignHelper->getQuoteCollection()
                    ->addFieldToFilter('product_id', $productId)
                    ->addFieldToFilter('customer_id', $customerId)
                    ->getLastItem();
                if (count($quote->getData()) > 0) {
                    $sellerId = $quote->getSellerId();
                } else {
                    $sellerId = $item->getLofSellerId();
                }
            } else {
                $sellerId = $item->getLofSellerId();
            }
            
            if ($this->helper->verifyCalculateCommission($item, $sellerId, $storeId)) {
                $sellerDatas = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)->load($sellerId, 'seller_id');
                $commission = $this->helper->getCommission($sellerId, $productId);
                $productSku = $item->getSku();
                $productName = $item->getName();
                $productPrice = $item->getData('row_total') + $item->getData('tax_amount');
                $discount_amount = $item->getDiscountAmount();
                $baseProductPrice = $item->getBasePrice();
                $priceCommission = $this->calculate->calculate($commission, $item);
                $sellerCommission = $priceCommission;
                $adminCommission = $item->getData('row_total')
                    + $item->getData('tax_amount')
                    - $item->getData('discount_amount')
                    - $priceCommission;

                $productQty = $item->getQtyOrdered();

                $sellerOrderItemsModel = $objectManager->create(\Lof\MarketPlace\Model\Orderitems::class);
                $foundSellerOrderItem = $sellerOrderItemsModel->getCollection()
                                    ->addFieldToFilter("seller_id", (int)$sellerId)
                                    ->addFieldToFilter("order_id", (int)$orderId)
                                    ->addFieldToFilter("product_id", (int)$productId)
                                    ->addFieldToFilter("order_item_id", (int)$itemId)
                                    ->getFirstItem();

                if ($foundSellerOrderItem && $foundSellerOrderItem->getId()) {
                    $sellerOrderItemsModel->load($foundSellerOrderItem->getId());
                    $sellerOrderItemsModel
                        ->setStatus($orderData->getData('status'));
                } else {
                    $sellerOrderItemsModel
                        ->setProductId($productId)
                        ->setProductSku($productSku)
                        ->setProductName($productName)
                        ->setSellerId($sellerId)
                        ->setOrderId($orderId)
                        ->setProductPrice($productPrice)
                        ->setBaseProductPrice($baseProductPrice)
                        ->setOrderItemId($itemId)
                        ->setProductQty($productQty)
                        ->setProductSku($productSku)
                        ->setProductName($productName)
                        ->setCommission($commission)
                        ->setOptions($customOptionArray)
                        ->setStatus($orderData->getData('status'))
                        ->setSellerCommissionOrder($sellerCommission)
                        ->setAdminCommissionOrder($adminCommission);
                }
                $sellerOrderItemsModel->getResource()->save($sellerOrderItemsModel);

                $country = $sellerDatas->getCountry();
                $nationalShippingAmount = $product->getNationalShippingAmount();
                $internationalShippingAmount = $product->getInternationalShippingAmount();

                if ($countryName == $country) {
                    $sellerShippingAmount = $nationalShippingAmount * $productQty;
                } else {
                    $sellerShippingAmount = $internationalShippingAmount * $productQty;
                }
                if (is_array($commission)) {
                    $sellerCommission = $commission['commission_amount'];
                } else {
                    $sellerCommission = $commission;
                }
                if (array_key_exists($sellerId, $sellerData)) {
                    $sellerData[$sellerId]['price'] += $productPrice - $discount_amount;
                    $sellerData[$sellerId]['commission'] += $sellerCommission;
                    $sellerData[$sellerId]['amount'] += $priceCommission;
                    $sellerData[$sellerId]['seller_id'] = $sellerId;
                    $sellerData[$sellerId]['shipping'] += $sellerShippingAmount;
                    $sellerData[$sellerId]['discount_amount'] += $discount_amount;
                } else {
                    $sellerData[$sellerId]['price'] = $productPrice - $discount_amount;
                    $sellerData[$sellerId]['commission'] = $sellerCommission;
                    $sellerData[$sellerId]['seller_id'] = $sellerId;
                    $sellerData[$sellerId]['amount'] = $priceCommission;
                    $sellerData[$sellerId]['shipping'] = $sellerShippingAmount;
                    $sellerData[$sellerId]['discount_amount'] = $discount_amount;
                }
            }
        }

        $customerOrderDetails = $objectManager->get(\Magento\Sales\Model\Order::class);
        $customerOrderData = $customerOrderDetails->load($orderId);

        if ($sellerData) {
            foreach ($sellerData as $sellerIds) {
                $sellerId = $sellerIds ['seller_id'];
                $customerSession = $objectManager->get(\Magento\Customer\Model\Session::class);
                if ($customerSession->isLoggedIn()) {
                    $customerId = $customerSession->getId();
                }
                $products = $objectManager->get(\Lof\MarketPlace\Model\Orderitems::class)->getCollection();
                $products->addFieldToSelect('*');
                $products->addFieldToFilter('order_id', $orderId);
                $products->addFieldToFilter('seller_id', $sellerId);
                $productIds = array_unique($products->getColumnValues('product_id'));

                $orderShippingAmount = $customerOrderData->getShippingAmount();
                $totalSellerShippingQty = $totalShippingQty = $shippingAmount = 0;
                foreach ($orderDetails->getAllItems() as $item) {
                    $itemProductId = $item->getProductId();
                    if (in_array($itemProductId, $productIds) && $item->getIsVirtual() != 1) {
                        $totalSellerShippingQty = $totalSellerShippingQty + $item->getQtyOrdered();
                    }
                    if ($item->getIsVirtual() != 1) {
                        $totalShippingQty = $totalShippingQty + $item->getQtyOrdered();
                    }
                }

                if (!empty($orderShippingAmount) && !empty($totalSellerShippingQty) && !empty($totalShippingQty)) {
                    $shippingAmount = round($orderShippingAmount * ($totalSellerShippingQty / $totalShippingQty), 2);
                }

                try {
                    $sellerOrderModel = $objectManager->create(\Lof\MarketPlace\Model\Order::class);
                    $foundSellerOrder = $sellerOrderModel->getCollection()
                                        ->addFieldToFilter("seller_id", $sellerId)
                                        ->addFieldToFilter("order_id", (int)$orderId)
                                        ->getFirstItem();
                    if ($foundSellerOrder && $foundSellerOrder->getId()) {
                        $sellerOrderModel->load($foundSellerOrder->getId());
                        $sellerOrderModel
                                    ->setStatus($orderData->getData('status'));
                    } else {
                        $sellerOrderModel
                            ->setSellerId($sellerIds['seller_id'])
                            ->setOrderId($orderId)
                            ->setSellerProductTotal($sellerIds['price'])
                            ->setCommission($sellerIds['commission'])
                            ->setSellerAmount($sellerIds['amount'])
                            ->setIncrementId($incrementId)
                            ->setOrderCurrencyCode($currencyCode)
                            ->setCustomerId($customerId)
                            ->setShippingAmount($shippingAmount)
                            ->setSellerShippingAmount(0)
                            ->setStatus($orderData->getData('status'))
                            ->setDiscountAmount($sellerIds['discount_amount']);

                        if ($this->helper->getCommissionType() == CommissionType::TYPE_PRODUCT_SHIPPING) {
                            $shippingCommission = $this->helper->getShippingCommission($sellerId);
                            $sellerShippingAmount = $this->calculateShippingCommission
                                ->calculate($shippingCommission, $shippingAmount);
                            $sellerAmount = $sellerIds['amount'] + $sellerShippingAmount;
                            $sellerOrderModel->setSellerAmount($sellerAmount);
                            $sellerOrderModel->setSellerShippingAmount($sellerShippingAmount);
                        }
                    }

                    $sellerOrderModel->getResource()->save($sellerOrderModel);
                    // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
                } catch (\Exception $e) {
                    //
                }

                $data = [];
                $sellerData = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)->load($sellerId, 'seller_id');
                $data['email'] = $sellerData->getData('email');
                $data['name'] = $sellerData->getData('name');
                $data['order_id'] = $customerOrderData->getIncrementId();
                $data['order_status'] = $orderData->getData('status');
                if ($this->helper->getConfig('email_settings/enable_send_email')) {
                    $this->sender->newOrder($data);
                }
            }
        }
    }

    /**
     * Get commission value
     *
     * @param float $commission
     * @param float $productPrice
     * @param int $productQty
     *
     * @return float
     */
    public function getCommissionValue($commissionProduct, $productPrice, $discountAmount)
    {
        $commission = 0;
        if (!is_array($commissionProduct)) {
            if ($commissionProduct != 0) {
                $commissionPerProduct = ($productPrice['row_total'] - $discountAmount) * ($commissionProduct / 100);
                $commission = $commissionPerProduct;
            } else {
                $commission = 0;
            }

            return $commission;
        }
        return $commission;
    }
}
