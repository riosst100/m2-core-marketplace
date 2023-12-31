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

namespace Lofmp\SplitOrder\Api;

/**
 * Interface QuoteHandlerInterface
 * @api
 */
interface QuoteHandlerInterface
{
    /**
     * Separate all items in quote into new quotes.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return mixed|array False if not split, or an array of array of split items
     */
    public function normalizeQuotes($quote);

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $attributeCode
     * @return string
     */
    public function getProductAttributes($product, $attributeCode);

    /**
     * Collect list of data addresses.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return mixed|array
     */
    public function collectAddressesData($quote);

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote $split
     * @return \Lofmp\SplitOrder\Api\QuoteHandlerInterface
     */
    public function setCustomerData($quote, $split);

    /**
     * Populate quotes with new data.
     *
     * @param array $quotes
     * @param \Magento\Quote\Model\Quote $split
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @param array $addresses
     * @param string $payment
     * @param string $sellerId
     * @return \Lofmp\SplitOrder\Api\QuoteHandlerInterface
     */
    public function populateQuote($quotes, $split, $items, $addresses, $payment, $sellerId);

    /**
     * Recollect order totals.
     *
     * @param array $quotes
     * @param \Magento\Quote\Model\Quote\Item[] $items
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $addresses
     * @param string $sellerId
     * @return \Lofmp\SplitOrder\Api\QuoteHandlerInterface
     */
    public function recollectTotal($quotes, $items, $quote, $addresses, $sellerId);

    /**
     * @param array $quotes
     * @param \Magento\Quote\Model\Quote $quote
     * @param string $sellerId
     * @param float $total
     */
    public function shippingAmount($quotes, $quote, $sellerId, $total = 0.0);

    /**
     * Set payment method.
     *
     * @param string $paymentMethod
     * @param \Magento\Quote\Model\Quote $split
     * @param string $payment
     * @return \Lofmp\SplitOrder\Api\QuoteHandlerInterface
     */
    public function setPaymentMethod($paymentMethod, $split, $payment);

    /**
     * Define checkout sessions.
     *
     * @param \Magento\Quote\Model\Quote $split
     * @param \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object|null $order
     * @param array $orderIds
     * @return \Lofmp\SplitOrder\Api\QuoteHandlerInterface
     */
    public function defineSessions($split, $order, $orderIds);
}
