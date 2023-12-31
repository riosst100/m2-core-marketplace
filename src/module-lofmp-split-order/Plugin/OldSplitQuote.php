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

namespace Lofmp\SplitOrder\Plugin;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\ManagerInterface;
use Lofmp\SplitOrder\Api\QuoteHandlerInterface;
use Lof\MarketPlace\Observer\OrderStatusChanged as MarketplaceOrderSubmitAfter;

class OldSplitQuote
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var QuoteHandlerInterface
     */
    private $quoteHandler;

    /**
     * @var MarketplaceOrderSubmitAfter
     */
    protected $marketplaceOrderObserver;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteFactory $quoteFactory
     * @param ManagerInterface $eventManager
     * @param QuoteHandlerInterface $quoteHandler
     * @param MarketplaceOrderSubmitAfter $marketplaceOrderObserver
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteFactory $quoteFactory,
        ManagerInterface $eventManager,
        QuoteHandlerInterface $quoteHandler,
        MarketplaceOrderSubmitAfter $marketplaceOrderObserver
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->eventManager = $eventManager;
        $this->quoteHandler = $quoteHandler;
        $this->marketplaceOrderObserver = $marketplaceOrderObserver;
    }

    /**
     * Places an order for a specified cart.
     *
     * @param QuoteManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param string $payment
     * @return mixed
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see \Magento\Quote\Api\CartManagementInterface
     */
    public function aroundPlaceOrder(QuoteManagement $subject, callable $proceed, $cartId, $payment = null)
    {
        $currentQuote = $this->quoteRepository->getActive($cartId);

        if ($currentQuote->getCheckoutMethod() === \Magento\Quote\Api\CartManagementInterface::METHOD_GUEST) {
            $currentQuote->setCustomerId(null);
            $currentQuote->setCustomerEmail($currentQuote->getBillingAddress()->getEmail());
            if ($currentQuote->getCustomerFirstname() === null && $currentQuote->getCustomerLastname() === null) {
                $currentQuote->setCustomerFirstname($currentQuote->getBillingAddress()->getFirstname());
                $currentQuote->setCustomerLastname($currentQuote->getBillingAddress()->getLastname());
                if ($currentQuote->getBillingAddress()->getMiddlename() === null) {
                    $currentQuote->setCustomerMiddlename($currentQuote->getBillingAddress()->getMiddlename());
                }
            }
            $currentQuote->setCustomerIsGuest(true);
            $groupId = $currentQuote->getCustomer()->getGroupId() ?: GroupInterface::NOT_LOGGED_IN_ID;
            $currentQuote->setCustomerGroupId($groupId);
        }

        // Separate all items in quote into new quotes.
        $quotes = $this->quoteHandler->normalizeQuotes($currentQuote);
        if (empty($quotes) || count($quotes) < 1) {
            return $proceed($cartId, $payment);
        }

        // Collect list of data addresses.
        $addresses = $this->quoteHandler->collectAddressesData($currentQuote);

        /** @var \Magento\Sales\Api\Data\OrderInterface[] $orders */
        $orders = [];
        $orderIds = [];
        $totalSellerQuotes = count($quotes);
        foreach ($quotes as $sellerId => $quoteItem) {
            if ($totalSellerQuotes > 1) {
                $items = $quoteItem['items'];
                /** @var \Magento\Quote\Model\Quote $split */
                $split = $this->quoteFactory->create();

                // Set all customer definition data.
                $this->quoteHandler->setCustomerData($currentQuote, $split);
                $this->toSaveQuote($split);

                // Map quote items.
                foreach ($items as $item) {
                    // Add item by item.
                    $item->setId(null);
                    $split->addItem($item);
                }
                $this->quoteHandler->populateQuote($quotes, $split, $items, $addresses, $payment, $sellerId);
                $this->toSaveQuote($split);
            } else {
                $split = $quoteItem['quote'];
            }
            // Dispatch event as Magento standard once per each quote split.
            $this->eventManager->dispatch(
                'checkout_submit_before',
                ['quote' => $split]
            );
            $order = $subject->submit($split);
            if (null == $order) {
                throw new LocalizedException(__('Please try to place the order again.'));
            } else {
                $orders[] = $order;
                $orderIds[$order->getId()] = $order->getIncrementId();
                $this->marketplaceOrderObserver->createSellerOrdersByOrderId($order->getId());
            }
        }

        $currentQuote->setIsActive(false);
        $this->toSaveQuote($currentQuote);

        $this->quoteHandler->defineSessions($split, $order, $orderIds);

        $this->eventManager->dispatch(
            'checkout_submit_all_after',
            ['orders' => $orders, 'quote' => $currentQuote]
        );
        return $this->getOrderKeys($orderIds);
    }

    /**
     * Save quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Lofmp\SplitOrder\Plugin\SplitQuote
     */
    private function toSaveQuote($quote)
    {
        $this->quoteRepository->save($quote);

        return $this;
    }

    /**
     * @param array $orderIds
     * @return array
     */
    private function getOrderKeys($orderIds)
    {
        $orderValues = [];
        foreach (array_keys($orderIds) as $orderKey) {
            $orderValues[] = (string)$orderKey;
        }
        return array_values($orderValues);
    }
}
