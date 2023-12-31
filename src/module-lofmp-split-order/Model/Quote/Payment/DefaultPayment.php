<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote\Payment;

use Magento\Framework\Exception\LocalizedException;
/**
 * Class DefaultPayment
 */
class DefaultPayment extends AbstractProcessPayment
{
    /**
     * @var string
     */
    protected $paymentType = "default";

    /**
     * @inheritdoc
     */
    public function process($currentQuote, $cartId = 0, $payment = null)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        $subject = $this->getSubjectObject();
        $quotes = $this->normalizeQuotes($currentQuote, $cartId, $payment);
        // Collect list of data addresses.
        $addresses = $this->getQuoteAddressData();
        if (!$addresses) {
            $addresses = $this->quoteHandler->collectAddressesData($currentQuote);
            $this->setQuoteAddressData($addresses);
        }
        /** @var \Magento\Sales\Api\Data\OrderInterface[] $orders */
        $orders = [];
        $orderIds = [];
        $totalSellerQuotes = count($quotes);
        $priceComparison = $this->enabledOfferProduct();
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
                if ($priceComparison) {
                    $this->processPriceComparrision($split, $quoteItem['items']);
                }
                // Dispatch event as Magento standard once per each quote split.
                $this->eventManager->dispatch(
                    'lofmp_splitorder_split_quote_after',
                    ['split' => $split, 'quote' => $quoteItem['quote'], 'old_items' => $quoteItem['items']]
                );
            } else {
                $split = $quoteItem['quote'];
            }
            // Dispatch event as Magento standard once per each quote split.
            $this->eventManager->dispatch(
                'checkout_submit_before',
                ['quote' => $split]
            );
            $order = $subject ? $subject->submit($split) : null;
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
     * @inheritdoc
     */
    public function checkProcessPayment($cartId = 0)
    {
        parent::checkProcessPayment($cartId);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function allowProcessBefore($cartId = 0)
    {
        parent::allowProcessBefore($cartId);
        return false;
    }

    /**
     * Check is enabled offer product or not
     *
     * @return bool
     */
    public function enabledOfferProduct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helperData = $objectManager->create(\Lofmp\SplitOrder\Helper\Data::class);
        $enabled = $helperData->isEnableModule('Lofmp_PriceComparison');
        return $enabled;
    }

    /**
     * process price comparrision
     *
     * @param mixed $quote
     * @param mixed $oldItems
     * @return void
     */
    public function processPriceComparrision($quote, $oldItems)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $assignHelper = $objectManager->create(\Lofmp\PriceComparison\Helper\ProcessItemQuote::class);
        $assignHelper->afterSplitQuote($quote, $oldItems);
    }
}
