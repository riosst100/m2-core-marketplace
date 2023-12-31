<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote\Payment;

use Lofmp\SplitOrder\Helper\Data;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\ManagerInterface;
use Lofmp\SplitOrder\Api\QuoteHandlerInterface;
use Lof\MarketPlace\Observer\OrderStatusChanged as MarketplaceOrderSubmitAfter;
/**
 * Class AbstractProcessPayment
 */
abstract class AbstractProcessPayment implements ProcessPaymentInterface
{
    /**
     * @var string
     */
    protected $paymentType = "default";

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var QuoteHandlerInterface
     */
    protected $quoteHandler;

    /**
     * @var MarketplaceOrderSubmitAfter
     */
    protected $marketplaceOrderObserver;

    /**
     * @var int
     */
    protected $_cartId = 0;

    /**
     * @var array
     */
    protected $_currentQuote = [];

    /**
     * @var array
     */
    protected $_quotes = [];

    /**
     * @var array
     */
    protected $_quoteAddresses = [];

    /**
     * @var \Magento\Quote\Model\QuoteManagement|mixed|object|null
     */
    protected $_subject = null;

    /**
     * @var callable|mixed|object|null
     */
    protected $_proceed = null;

    /**
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteFactory $quoteFactory
     * @param ManagerInterface $eventManager
     * @param QuoteHandlerInterface $quoteHandler
     * @param MarketplaceOrderSubmitAfter $marketplaceOrderObserver
     * @param string $paymentType = null
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        QuoteFactory $quoteFactory,
        ManagerInterface $eventManager,
        QuoteHandlerInterface $quoteHandler,
        MarketplaceOrderSubmitAfter $marketplaceOrderObserver,
        $paymentType = null
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->eventManager = $eventManager;
        $this->quoteHandler = $quoteHandler;
        $this->marketplaceOrderObserver = $marketplaceOrderObserver;
        $void = $paymentType ? $this->setPayment($paymentType) : null;
    }

    /**
     * @inheritdoc
     */
    public function process($currentQuote, $cartId = 0, $payment = null)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function normalizeQuotes($currentQuote, $cartId = 0, $payment = null)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        $cartId = $this->_cartId;
        if ($currentQuote && !isset($this->_quotes[$this->_cartId])) {
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
            $this->_quotes[$this->_cartId] = $this->quoteHandler->normalizeQuotes($currentQuote);
        }
        return isset($this->_quotes[$this->_cartId]) ? $this->_quotes[$this->_cartId] : [];
    }

    /**
     * @inheritdoc
     */
    public function checkProcessPayment($cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function checkProcessArroundPayment($cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function allowProcessBefore($cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentQuote($cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        if (!isset($this->_currentQuote[$this->_cartId]) && $this->_cartId) {
            $this->_currentQuote[$this->_cartId] = $this->quoteRepository->getActive($this->_cartId);
        }
        return isset($this->_currentQuote[$this->_cartId]) ? $this->_currentQuote[$this->_cartId] : null;
    }

    /**
     * @inheritdoc
     */
    public function setCurrentQuote($currentQuote, $cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        $this->_currentQuote[$this->_cartId] = $currentQuote;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setCartId($cartId)
    {
        $this->_cartId = $cartId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSubjectObject($subject)
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setPayment($payment)
    {
        $this->paymentType = $payment;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubjectObject()
    {
        return $this->_subject;
    }

    /**
     * @inheritdoc
     */
    public function setQuoteAddressData($addresses)
    {
        $this->_quoteAddresses = $addresses;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQuoteAddressData()
    {
        return $this->_quoteAddresses;
    }

    /**
     * @inheritdoc
     */
    public function setQuotes($quotes, $cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        $this->_quotes[$this->_cartId] = $quotes;
        return $this;
    }

    /**
     * Save quote
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Lofmp\SplitOrder\Plugin\SplitQuote
     */
    protected function toSaveQuote($quote)
    {
        $this->quoteRepository->save($quote);

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function getOrderKeys($orderIds)
    {
        $orderValues = [];
        foreach (array_keys($orderIds) as $orderKey) {
            $orderValues[] = (string)$orderKey;
        }
        return array_values($orderValues);
    }

    /**
     * @inheritdoc
     */
    public function setProceed($proceed)
    {
        $this->_proceed = $proceed;
        return $this;
    }

    /**
     *  @inheritdoc
     */
    public function getProceed()
    {
        return $this->_proceed;
    }
}
