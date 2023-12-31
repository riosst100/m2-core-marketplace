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

namespace Lof\MarketPlace\Helper;

use Lof\MarketPlace\Model\OrderFactory as MarketOrderFactory;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\Source\CommissionType;
use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Model\OrderFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class SellerOrderHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var MarketOrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderFactory
     */
    protected $coreOrderFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Model\CalculateCommission
     */
    protected $calculate;

    /**
     * @var \Lof\MarketPlace\Model\CalculateShippingCommission
     */
    protected $calculateShippingCommission;

    /**
     * @var \Lof\MarketPlace\Model\SellerProductFactory
     */
    protected $sellerProductFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * SellerOrderHelper constructor.
     *
     * @param Data $helper
     * @param \Lof\MarketPlace\Model\CalculateCommission $calculate
     * @param \Lof\MarketPlace\Model\CalculateShippingCommission $calculateShippingCommission
     * @param \Lof\MarketPlace\Model\SellerProductFactory $sellerProductFactory
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Magento\Framework\App\Helper\Context $context
     * @param SellerFactory $sellerFactory
     * @param ProductFactory $productFactory
     * @param OrderFactory $coreOrderFactory
     * @param MarketOrderFactory $orderFactory
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\CalculateShippingCommission $calculateShippingCommission,
        \Lof\MarketPlace\Model\SellerProductFactory $sellerProductFactory,
        \Lof\MarketPlace\Model\Sender $sender,
        \Magento\Framework\App\Helper\Context $context,
        SellerFactory $sellerFactory,
        ProductFactory $productFactory,
        OrderFactory $coreOrderFactory,
        MarketOrderFactory $orderFactory
    ) {
        $this->sender = $sender;
        $this->calculate = $calculate;
        $this->calculateShippingCommission = $calculateShippingCommission;
        $this->helper = $helper;
        $this->sellerFactory = $sellerFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->orderFactory = $orderFactory;
        $this->coreOrderFactory = $coreOrderFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * @param int $orderId
     * @param bool $checkOrderExists
     * @param bool $checkOrderSellerExits
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function createSellerOrder($orderId, $checkOrderExists = true, $checkOrderSellerExits = false)
    {
        $returnData = false;
        if (!$orderId) {
            return $returnData;
        }

        if ($checkOrderExists && !$checkOrderSellerExits) {
            $sellerOrderModel = $this->orderFactory->create();
            $collection = $sellerOrderModel->getCollection()
                ->addFieldToFilter("order_id", $orderId);
            if (0 < $collection->count()) {
                $returnData = true;
                return $returnData;
            }
        }
        $orderDetails = $this->coreOrderFactory->create();
        $orderData = $orderDetails->load($orderId);
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($orderData) {
            $currencyCode = $orderData->getOrderCurrencyCode();
            $incrementId = $orderDetails->getIncrementId();
            $customerId = $orderDetails->getCustomerId();
            $orderItems = $orderData->getAllItems();
            $storeId = $orderData->getStoreId();
            $sellerData = [];

            if ($orderItems) {
                //$calculate_parent_only = $this->helper->calculateCommissionForParent();
                foreach ($orderItems as $item) {
                    $productId = $item->getProductId();
                    $itemId = $item->getItemId();
                    $customOptions = $item->getProductOptions();
                    $customOptionArray = json_encode($customOptions);
                    $product = $this->productFactory->create()->load($productId);
                    $sellerId = $item->getLofSellerId();
                    if ($checkOrderSellerExits) {
                        $sellerOrderModelChecking = $this->orderFactory->create();
                        $collection = $sellerOrderModelChecking->getCollection()
                            ->addFieldToFilter("order_id", $orderId)
                            ->addFieldToFilter("seller_id", $sellerId);

                        if (0 < $collection->count()) {
                            continue;
                        }
                    }

                    $priceComparison = $this->helper->isEnableModule('Lofmp_PriceComparison');
                    if ($priceComparison) {
                        /** @phpstan-ignore-next-line */
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
                        $sellerDatas = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)
                            ->load($sellerId, 'seller_id');
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
                        if ($orderData->getBillingAddress()->getCountryId() == $country) {
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
                            $sellerData [$sellerId] ['price'] += $productPrice - $discount_amount;
                            $sellerData [$sellerId] ['commission'] += $sellerCommission;
                            $sellerData [$sellerId] ['amount'] += $priceCommission;
                            $sellerData [$sellerId] ['seller_id'] = $sellerId;
                            $sellerData [$sellerId] ['shipping'] += $sellerShippingAmount;
                            $sellerData [$sellerId] ['discount_amount'] += $discount_amount;
                        } else {
                            $sellerData [$sellerId] ['price'] = $productPrice - $discount_amount;
                            $sellerData [$sellerId] ['commission'] = $sellerCommission;
                            $sellerData [$sellerId] ['seller_id'] = $sellerId;
                            $sellerData [$sellerId] ['amount'] = $priceCommission;
                            $sellerData [$sellerId] ['shipping'] = $sellerShippingAmount;
                            $sellerData [$sellerId] ['discount_amount'] = $discount_amount;
                        }
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
                    if (!empty($orderShippingAmount)
                        && !empty($totalSellerShippingQty)
                        && !empty($totalShippingQty)
                    ) {
                        $shippingAmount = round(
                            $orderShippingAmount * ($totalSellerShippingQty / $totalShippingQty),
                            2
                        );
                    }
                    $sellerOrderModel = $this->orderFactory->create();
                    $foundSellerOrder = $sellerOrderModel->getCollection()
                                        ->addFieldToFilter("seller_id", $sellerId)
                                        ->addFieldToFilter("order_id", (int)$orderId)
                                        ->getFirstItem();
                    if ($foundSellerOrder && $foundSellerOrder->getId()) {
                        $sellerOrderModel->load($foundSellerOrder->getId());
                        $sellerOrderModel
                                ->setStatus($orderData->getData('status'));
                    } else {
                        $sellerOrderModel->setSellerId($sellerIds ['seller_id'])
                            ->setOrderId($orderId)
                            ->setSellerProductTotal($sellerIds ['price'])
                            ->setCommission($sellerIds ['commission'])
                            ->setSellerAmount($sellerIds ['amount'])
                            ->setIncrementId($incrementId)
                            ->setOrderCurrencyCode($currencyCode)
                            ->setCustomerId($customerId)
                            ->setShippingAmount($shippingAmount)
                            ->setStatus($orderData->getData('status'))
                            ->setDiscountAmount($sellerIds ['discount_amount']);

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
                    //$sellerOrderModel->save();
                    $returnData = true;

                    $data = [];
                    $sellerData = $objectManager->get(\Lof\MarketPlace\Model\Seller::class)
                        ->load($sellerId, 'seller_id');
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

        return $returnData;
    }

    /**
     * @param $productId
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSellerByProductId($productId)
    {
        try {
            $sellerProduct = $this->sellerProductFactory->create()->load($productId, 'product_id');
            return $this->sellerFactory->create()->load($sellerProduct->getSellerId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
