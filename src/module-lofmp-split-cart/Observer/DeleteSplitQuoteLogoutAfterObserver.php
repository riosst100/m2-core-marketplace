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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitCart\Observer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DeleteSplitQuoteLogoutAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Lofmp\SplitCart\Helper\ConfigData
     */
    private $moduleConfig;

    /**
     * @var \Lofmp\SplitCart\Model\QuoteFactory
     */
    protected $splitQuoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Lofmp\SplitCart\Helper\ConfigData $moduleConfig
     * @param \Lofmp\SplitCart\Model\QuoteFactory $splitQuoteFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $itemFactory
     */
    public function __construct(
        \Lofmp\SplitCart\Helper\ConfigData $moduleConfig,
        \Lofmp\SplitCart\Model\QuoteFactory $splitQuoteFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->splitQuoteFactory = $splitQuoteFactory;
        $this->quoteFactory = $quoteFactory;
        $this->_objectManager = $objectManager;
        $this->_cart = $cart;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->moduleConfig->isEnabled()) {
            return $this;
        }

        try {
            /** @var \Magento\Quote\Model\Quote $parentQuote */
            $parentQuote = $this->_cart->getQuote();
            $parentQuoteId = $parentQuote->getId();

            if (!$parentQuote || !$parentQuote->hasItems()) {
                return $this;
            }

            $splitQuoteCollection = $this->splitQuoteFactory->create()->getCollection()
                ->addFieldToFilter('parent_id', $parentQuoteId)
                ->addFieldToFilter('is_ordered', 0);
            if (!$splitQuoteCollection || $splitQuoteCollection->getSize() == 0) {
                return $this;
            }

            foreach ($splitQuoteCollection as $splitQuote) {
                $this->quoteFactory->create()->load($splitQuote->getQuoteId())->delete();
            }
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
    }
}
