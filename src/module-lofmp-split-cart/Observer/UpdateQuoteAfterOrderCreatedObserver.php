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
class UpdateQuoteAfterOrderCreatedObserver implements \Magento\Framework\Event\ObserverInterface
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
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Checkout\Model\Cart $cart
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
            $splitQuoteId = $order->getQuoteId();
            $splitQuoteCollection = $this->splitQuoteFactory->create()->getCollection()
                ->addFieldToFilter('quote_id', $splitQuoteId)
                ->addFieldToFilter('is_ordered', 0)
                ->addFieldToFilter('is_active', 1);
            if ($splitQuoteCollection && $splitQuoteCollection->getSize() > 0) {
                foreach ($splitQuoteCollection as $splitQuote) {
                    $this->splitQuoteFactory->create()->load($splitQuote->getId())
                        ->setIsActive(0)
                        ->setIsOrdered(1)
                        ->save();
                }
            }

            $splitQuoteModel = $this->quoteFactory->create()->load($splitQuoteId);
            $orderedItems = $splitQuoteModel->getAllItems();
            $parentQuoteId = $splitQuoteModel->getParentId();
            if ($orderedItems && $this->_cart->getQuote()->getId() == $parentQuoteId) {
                foreach ($orderedItems as $item) {
                    if ($item->getParentId()) {
                        $this->_cart->removeItem($item->getParentId());
                    }
                }
                $this->_cart->save();
            }
        } catch (\Exception $e) {
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
    }
}
