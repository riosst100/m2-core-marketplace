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
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\MultiShipping\Observer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UpdateShippingInfoAfterOrderCreatedObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Lofmp\MultiShipping\Helper\Data
     */
    private $moduleConfig;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Lofmp\MultiShipping\Helper\Data $moduleConfig
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Lofmp\MultiShipping\Helper\Data $moduleConfig,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->quoteFactory = $quoteFactory;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleConfig->isEnabled()) {
            return $this;
        }

        try {
            $order = $observer->getOrder();
            $quoteId = $order->getQuoteId();
            $quote = $this->quoteFactory->create()->load($quoteId);
            if ($quote && $quote->getId()) {
                $shippingMethod = $order->getShippingMethod();
                $shippingRate = $quote->getShippingAddress()->getShippingRateByCode($shippingMethod);
                if ($shippingRate) {
                    $mpInfo = $shippingRate->getMpInfo();
                    if ($mpInfo) {
                        $order->setMpInfo($mpInfo)->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
    }
}
