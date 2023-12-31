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
 * @package    Lofmp_SplitOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrder\Model;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Lofmp\SplitOrder\Api\QuoteHandlerInterface;
use Lofmp\SplitOrder\Helper\Data as HelperData;
use Lofmp\SplitOrder\Api\ExtensionAttributesInterface;

class QuoteHandler implements QuoteHandlerInterface
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var HelperData
     */
    private $helperData;

    /**
     * @var ExtensionAttributesInterface
     */
    private $extensionAttributes;

    /**
     * QuoteHandler constructor.
     * @param CheckoutSession $checkoutSession
     * @param HelperData $helperData
     * @param ExtensionAttributesInterface $extensionAttributes
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        HelperData $helperData,
        ExtensionAttributesInterface $extensionAttributes
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        $this->extensionAttributes = $extensionAttributes;
    }

    /**
     * @inheritdoc
     */
    public function normalizeQuotes($quote)
    {
        if (!$this->helperData->isActive()) {
            return [];
        }
        $attributeCode = $this->helperData->getAttributes();
        if (empty($attributeCode)) {
            return [];
        }
        $groups = [];
        $priceComparison = $this->enabledOfferProduct();
        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            $attribute = $item->getLofSellerId();
            if (!$attribute) {
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $item->getProduct();
                $attribute = $this->getProductAttributes($product, $attributeCode);
            }
            if (!$attribute) {
                $attribute = "0";
            }
            if ($priceComparison) {
                // @phpstan-ignore-next-line
                $priceComparisonQuote = $this->getOfferProduct($product->getId(), $item->getId());
                if ($priceComparisonQuote) {
                    $attribute = $priceComparisonQuote->getSellerId();
                }
            }
            $item->setSellerId((int)$attribute);
            $groups[$attribute]['items'][] = $item;
            $groups[$attribute]['quote'] = $quote;
            $groups[$attribute]['seller_id'] = $attribute;
        }
        // If order have more than one different attribute values.
        if (count($groups) >= 1) {
            return $groups;
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getProductAttributes($product, $attributeCode)
    {
        $extensionAttribute = $this->extensionAttributes->loadValue($product, $attributeCode);
        if ($extensionAttribute !== false) {
            return $extensionAttribute;
        }
        $attributeObject = $product->getResource()->getAttribute($attributeCode);

        $attributeValue = $attributeObject->getFrontend()->getValue($product);
        if ($attributeValue instanceof \Magento\Framework\Phrase) {
            return $attributeValue->__toString();
        }
        return $attributeValue;
    }

    /**
     * @inheritdoc
     */
    public function collectAddressesData($quote)
    {
        $billing = $quote->getBillingAddress()->getData();
        unset($billing['id']);
        unset($billing['quote_id']);

        $shipping = $quote->getShippingAddress()->getData();
        unset($shipping['id']);
        unset($shipping['quote_id']);

        return [
            'payment' => $quote->getPayment()->getMethod(),
            'billing' => $billing,
            'shipping' => $shipping
        ];
    }

    /**
     * @inheritdoc
     */
    public function setCustomerData($quote, $split)
    {
        $split->setStoreId($quote->getStoreId());
        $split->setCustomer($quote->getCustomer());
        $split->setCustomerIsGuest($quote->getCustomerIsGuest());

        if ($quote->getCheckoutMethod() === CartManagementInterface::METHOD_GUEST) {
            $split->setCustomerId(null);
            $split->setCustomerEmail($quote->getBillingAddress()->getEmail());
            if ($quote->getCustomerFirstname() === null && $quote->getCustomerLastname() === null) {
                $split->setCustomerFirstname($quote->getBillingAddress()->getFirstname());
                $split->setCustomerLastname($quote->getBillingAddress()->getLastname());
                if ($quote->getBillingAddress()->getMiddlename() === null) {
                    $split->setCustomerMiddlename($quote->getBillingAddress()->getMiddlename());
                }
            }
            $split->setCustomerIsGuest(true);
            $split->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);
            $groupId = $quote->getCustomer()->getGroupId() ?: GroupInterface::NOT_LOGGED_IN_ID;
            $split->setCustomerGroupId($groupId);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function populateQuote($quotes, $split, $items, $addresses, $payment, $sellerId)
    {
        $this->recollectTotal($quotes, $items, $split, $addresses, $sellerId);
        // Set payment method.
        $this->setPaymentMethod($split, $addresses['payment'], $payment);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function recollectTotal($quotes, $items, $quote, $addresses, $sellerId)
    {
        $tax = 0.0;
        $discount = 0.0;
        $finalPrice = 0.0;
        foreach ($items as $item) {
            // Retrieve values.
            $tax += $item->getData('tax_amount');
            $discount += $item->getData('discount_amount');

            $finalPrice += ($item->getPrice() * $item->getQty());
        }

        // Set addresses.
        $quote->getBillingAddress()->setData($addresses['billing']);
        $quote->getShippingAddress()->setData($addresses['shipping']);

        // Add shipping amount if product is not virtual.
        $shipping = $this->shippingAmount($quotes, $quote, $sellerId);
        // Recollect totals into the quote.
        foreach ($quote->getAllAddresses() as $address) {
            // Build grand total.
            $grandTotal = (($finalPrice + $shipping + $tax) - $discount);

            $address->setBaseSubtotal($finalPrice);
            $address->setSubtotal($finalPrice);
            $address->setDiscountAmount($discount);
            $address->setTaxAmount($tax);
            $address->setBaseTaxAmount($tax);
            $address->setBaseGrandTotal($grandTotal);
            $address->setGrandTotal($grandTotal);
        }
        return $this;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function shippingAmount($quotes, $quote, $sellerId, $total = 0.0)
    {
        // Add shipping amount if product is not virtual.
        if ($quote->hasVirtualItems() === true) {
            return $total;
        }
        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress) {
            return $total;
        }

        $shippingTotals = $shippingAddress->getShippingAmount();
        $shippingMethod = $shippingAddress->getShippingMethod();
        $shippingRate = $shippingAddress->getShippingRateByCode($shippingMethod);
        if (!$shippingRate) {
            return $total;
        }
        $carrier = $shippingRate->getCarrier();

        // If not, set shipping to one order only.
        if (!$this->helperData->isShippingSplit()) {
            static $process = 1;

            if ($process > 1) {
                // Set zero price to next orders.
                $quote->getShippingAddress()->setShippingAmount($total);
                return $total;
            }
            $process++;

            return $shippingTotals;
        } else {
            // Compatible with Lofmp_MultiShipping
            if ($carrier === 'seller_rates') {
                $mpInfo = $shippingRate->getMpInfo();
                if ($mpInfo) {
                    $mpInfo = json_decode($mpInfo, true);
                    $sellerId = (int)$sellerId;
                    if ($sellerId == 0) {
                        $sellerId = 'admin';
                    }
                    $total = isset($mpInfo[$sellerId]['price']) ? (float)$mpInfo[$sellerId]['price'] : $total;
                    $shippingMethod = isset($mpInfo[$sellerId]['code'])
                        ? $mpInfo[$sellerId]['code']
                        : $shippingMethod;
                    $carrierTitle = isset($mpInfo[$sellerId]['carrier_title'])
                        ? $mpInfo[$sellerId]['carrier_title']
                        : $shippingRate->getCarrierTitle();
                    $methodTitle = isset($mpInfo[$sellerId]['method_title'])
                        ? $mpInfo[$sellerId]['method_title']
                        : $shippingRate->getMethodTitle();
                    $quote->getShippingAddress()->setShippingAmount($total);
                    $quote->getShippingAddress()->setBaseShippingAmount($total);
                    $quote->getShippingAddress()->setShippingInclTax($total);
                    $quote->getShippingAddress()->setBaseShippingInclTax($total);
                    $quote->getShippingAddress()->setShippingMethod($shippingMethod);
                    $quote->getShippingAddress()->setShippingDescription($carrierTitle . ' - ' . $methodTitle);
                    return $total;
                }
            }

            if ($shippingTotals > 0) {
                // Divide shipping to each order.
                $total = (float)($shippingTotals / count($quotes));
                $quote->getShippingAddress()->setShippingAmount($total);
                $quote->getShippingAddress()->setBaseShippingAmount($total);
                $quote->getShippingAddress()->setShippingInclTax($total);
                $quote->getShippingAddress()->setBaseShippingInclTax($total);
            }
        }

        return $total;
    }

    /**
     * @inheritdoc
     */
    public function setPaymentMethod($split, $payment, $paymentMethod)
    {
        $split->getPayment()->setMethod($payment);

        if ($paymentMethod) {
            $split->getPayment()->setQuote($split);
            $data = $paymentMethod->getData();
            $split->getPayment()->importData($data);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function defineSessions($split, $order, $orderIds)
    {
        $this->checkoutSession->setLastQuoteId($split->getId());
        $this->checkoutSession->setLastSuccessQuoteId($split->getId());
        $this->checkoutSession->setLastOrderId($order->getId());
        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
        $this->checkoutSession->setLastOrderStatus($order->getStatus());
        $this->checkoutSession->setOrderIds($orderIds);

        return $this;
    }

    /**
     * Check is enabled offer product or not
     *
     * @return bool
     */
    public function enabledOfferProduct()
    {
        $enabled = $this->helperData->isEnableModule('Lofmp_PriceComparison');
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
}
