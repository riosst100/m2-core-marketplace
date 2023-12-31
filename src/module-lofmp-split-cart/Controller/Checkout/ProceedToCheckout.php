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

namespace Lofmp\SplitCart\Controller\Checkout;

class ProceedToCheckout extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Lofmp\SplitCart\Model\QuoteFactory
     */
    protected $splitQuoteFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteIdMask
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @param Context $context
     * @param \Lofmp\SplitCart\Model\QuoteFactory $splitQuoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lofmp\SplitCart\Model\QuoteFactory $splitQuoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->splitQuoteFactory = $splitQuoteFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteFactory = $quoteFactory;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_url = $url;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $sellerId = (int)$this->getRequest()->getParam('sid');
        if (!$this->isValidSellerId($sellerId)) {
            /*Redirect to cart page*/
            $this->messageManager->addErrorMessage(__('Invalid request!'));
            $redirectUrl = $this->_url->getUrl('checkout/cart');
            return $this->getResponse()->setRedirect($redirectUrl);
        }

        $quote = $this->getQuote();
        $quoteId = $quote->getId();
        $splitQuoteCollection = $this->splitQuoteFactory->create()->getCollection()
            ->addFieldToFilter('parent_id', $quoteId)
            ->addFieldToFilter('is_ordered', 0);

        if ($splitQuoteCollection && $splitQuoteCollection->getSize() > 0) {
            foreach ($splitQuoteCollection as $splitQuoteItem) {
                $this->splitQuoteFactory->create()->load($splitQuoteItem->getId())->setIsActive(0)->save();
                $this->_quoteFactory->create()->load($splitQuoteItem->getQuoteId())->setIsActive(0)->save();
            }
            $splitQuote = $this->splitQuoteFactory->create()->getCollection()
                ->addFieldToFilter('parent_id', $quoteId)
                ->addFieldToFilter('is_ordered', 0)
                ->addFieldToFilter('seller_id', $sellerId)
                ->getFirstItem();
            if ($splitQuote && $splitQuote->getData()) {
                $this->splitQuoteFactory->create()->load($splitQuote->getId())->setIsActive(1)->save();
                $this->_quoteFactory->create()->load($splitQuote->getQuoteId())->setIsActive(1)->save();
            } else {
                $this->createNewQuote($sellerId);
            }
        } else {
            $this->createNewQuote($sellerId);
        }

        $isMultiShipping = $this->getRequest()->getParam('multishipping');
        if ($isMultiShipping == 1) {
            $redirectUrl = $this->_url->getUrl('multishipping/checkout', ['_secure' => true]);
        } else {
            $redirectUrl = $this->_url->getUrl('checkout/index/index');
        }

        return $this->getResponse()->setRedirect($redirectUrl);
    }

    /**
     * @param int $sellerId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createNewQuote($sellerId)
    {
        $orgQuote = $this->getQuote();
        $quote = $this->_quoteFactory->create();
        $quote->setStore($orgQuote->getStore());
        $quote->setCurrency();
        $quote->assignCustomer($orgQuote->getCustomer());

        //add items in quote
        foreach ($orgQuote->getAllItems() as $item) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $sellerIdItem = $item->getLofSellerId() ? $item->getLofSellerId() : $item->getProduct()->getSellerId();
            if ($sellerIdItem == $sellerId) {
                $parentId = $item->getId();
                $item->setId(null);

                $options = $item->getOptions();
                $optionsResult = [];
                foreach ($options as $option) {
                    /** @var \Magento\Quote\Model\Quote\Item\Option $option */
                    $option->setId(null);
                    $optionsResult[] = $option;
                }
                $item->setOptions($optionsResult);
                $item->setParentId($parentId);
                $quote->addItem($item);
            }
        }
        $quote->setBillingAddress($orgQuote->getBillingAddress());
        $quote->setShippingAddress($orgQuote->getShippingAddress());

        // Collect Rates and Set Shipping & Payment Method
        $quote->setPaymentMethod($orgQuote->getPaymentMethod());
        $quote->setInventoryProcessed(false);
        $quote->setParentId($orgQuote->getId());
        $quote->save();

        $splitQuote = $this->splitQuoteFactory->create();
        $splitQuote->setParentId($orgQuote->getId())
            ->setQuoteId($quote->getId())
            ->setSellerId($sellerId)
            ->setIsActive(1)
            ->setIsOrdered(0)
            ->save();

        // Collect Totals & Save Quote
        $quote->collectTotals()->save();

        if (!$quote->getCustomerId()) {
            /** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create();
            $quoteIdMask->setQuoteId($quote->getId())->save();
        }
        return $quote->getId();
    }

    /**
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * @param int $sellerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function isValidSellerId($sellerId)
    {
        $quote = $this->getQuote();
        if (!$quote || !$quote->hasItems()) {
            return false;
        }
        // For Admin's products.
        if ($sellerId == 0) {
            return true;
        }

        $items = $quote->getItems();
        foreach ($items as $item) {
            $sellerIdItem = $item->getLofSellerId() ? $item->getLofSellerId() : $item->getSellerId();
            if ($sellerIdItem == $sellerId) {
                return true;
            }
        }

        return false;
    }
}
