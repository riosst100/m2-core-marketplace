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
 * @package    Lofmp_SplitOrderPaypal
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrderPaypal\Observer;

use Lofmp\SplitOrder\Api\QuoteHandlerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Paypal\Model\Config;
use Magento\Quote\Model\QuoteFactory;

class BeforePlaceOrderObserver implements ObserverInterface
{
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
     * @param Config $paypalConfig
     */
    public function __construct(
        QuoteHandlerInterface $quoteHandler,
        QuoteFactory $quoteFactory,
        Config $paypalConfig
    ) {
        $this->quoteHandler = $quoteHandler;
        $this->quoteFactory = $quoteFactory;
        $this->paypalConfig = $paypalConfig;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        if (!$this->paypalConfig->isMethodActive($order->getPayment()->getMethod())) {
            return;
        }

        $currentQuote = $this->quoteFactory->create()->load($order->getQuoteId());
        $quotes = $this->quoteHandler->normalizeQuotes($currentQuote);

        // Do not split order if only Admin's products
        if (empty($quotes) || (count($quotes) == 1 && isset($quotes[0]['seller_id']) && $quotes[0]['seller_id'] == 0)) {
            return;
        }

//        if (count($quotes) == 1 && !isset($quotes[0]['seller_id'])) {
//            return;
//        }

        $order->setPpIsMainOrder(1);
    }
}
