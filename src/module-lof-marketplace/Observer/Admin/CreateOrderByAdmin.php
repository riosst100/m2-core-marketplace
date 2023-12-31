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

class CreateOrderByAdmin implements ObserverInterface
{

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @param QuoteFactory $quoteFactory
     */
    public function __construct(
        QuoteFactory $quoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        $orderByAdmin = $quote->getData("order_by_admin");
        if (!$orderByAdmin) {
            $quote->setData("order_by_admin", 1)->save();
        }
    }
}
