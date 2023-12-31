<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote;

use Lofmp\SplitOrder\Helper\Data;

/**
 * Class AbstractProcessQuote
 */
abstract class AbstractProcessQuote implements ProcessQuoteInterface
{
    /**
     * @var string
     */
    const DEFAULT_PAYMENT = "default";

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $availablePayments = [
        "default" => \Lofmp\SplitOrder\Model\Quote\Payment\DefaultPayment::class,
        "paypal" => \Lofmp\SplitOrder\Model\Quote\Payment\PaypalPayment::class,
        "razorpay" => \Lofmp\SplitOrder\Model\Quote\Payment\RazorpayPayment::class
    ];

    /**
     * @var array
     */
    protected $_payments = [];

    /**
     * @var array
     */
    protected $_currentQuoteAddresses = [];

    /**
     * @var array
     */
    protected $_quotes = [];

    /**
     * @var \Magento\Quote\Model\QuoteManagement|mixed|object|null
     */
    protected $_subject = null;

    /**
     * @var callable|mixed|object|null
     */
    protected $_proceed = null;

    /**
     * ProcessQuoteAbstract constructor.
     *
     * @param Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array|null $availablePayments = null
     */
    public function __construct(
        Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $availablePayments = null
    ) {
        $this->helperData = $helperData;
        if ($availablePayments) {
            $this->availablePayments = $availablePayments;
        }
        $this->objectManager = $objectManager;
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
    public function setProceed($proceed)
    {
        $this->_proceed = $proceed;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPayment($payment)
    {
        if (!$payment) {
            $payment = self::DEFAULT_PAYMENT;
        }
        if (!isset($this->availablePayments[$payment])) {
            $payment = self::DEFAULT_PAYMENT;
        }
        if ($this->availablePayments && !isset($this->_payments[$payment]) && isset($this->availablePayments[$payment])) {
            $this->_payments[$payment] = $this->objectManager->create($this->availablePayments[$payment]);
        }
        return isset($this->_payments[$payment]) ? $this->_payments[$payment] : null;
    }

    /**
     * @inheritdoc
     */
    public function process($cartId, $payment, $currentQuote, $paymentMethod = null)
    {
        $foundPayment = $this->getPayment($payment);
        if ($foundPayment) {
            if ($this->_subject) {
                $foundPayment->setSubjectObject($this->_subject);
            }
            if ($this->_proceed) {
                $foundPayment->setProceed($this->_proceed);
            }
            $results = $foundPayment->process($currentQuote, $cartId, $paymentMethod);
            $this->_payments[$payment] = $foundPayment;
            return $results;
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function normalizeQuotes($cartId, $payment, $currentQuote)
    {
        $foundPayment = $this->getPayment($payment);
        if ($foundPayment) {
            if ($this->_subject) {
                $foundPayment->setSubjectObject($this->_subject);
            }
            if ($this->_proceed) {
                $foundPayment->setProceed($this->_proceed);
            }
            $quotes = $foundPayment->normalizeQuotes($currentQuote, $cartId, $payment);
            return $quotes;
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getCurrentQuote($cartId, $payment)
    {
        if (isset($payment['method'])) {
            $payment = $payment['method'];
        }
        $foundPayment = $this->getPayment($payment);
        if ($foundPayment && !$this->_quotes) {
            if ($this->_subject) {
                $foundPayment->setSubjectObject($this->_subject);
            }
            if ($this->_proceed) {
                $foundPayment->setProceed($this->_proceed);
            }
            $this->_quotes = $foundPayment->getCurrentQuote($cartId);
            $this->_payments[$payment] = $foundPayment;
        }
        return $this->_quotes;
    }

    /**
     * @inheritdoc
     */
    public function checkProcessPayment($cartId, $payment)
    {
        $foundPayment = $this->getPayment($payment);
        if ($foundPayment) {
            if ($this->_subject) {
                $foundPayment->setSubjectObject($this->_subject);
            }
            if ($this->_proceed) {
                $foundPayment->setProceed($this->_proceed);
            }
            $result = $foundPayment->checkProcessPayment($cartId);
            $this->_payments[$payment] = $foundPayment;
            return $result;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function checkProcessArround($cartId, $payment)
    {
        $foundPayment = $this->getPayment($payment);
        if ($foundPayment) {
            if ($this->_subject) {
                $foundPayment->setSubjectObject($this->_subject);
            }
            if ($this->_proceed) {
                $foundPayment->setProceed($this->_proceed);
            }
            $result = $foundPayment->checkProcessArroundPayment($cartId);
            $this->_payments[$payment] = $foundPayment;
            return $result;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function allowProcessBefore($cartId, $payment)
    {
        $foundPayment = $this->getPayment($payment);
        if ($foundPayment) {
            if ($this->_subject) {
                $foundPayment->setSubjectObject($this->_subject);
            }
            if ($this->_proceed) {
                $foundPayment->setProceed($this->_proceed);
            }
            $result = $foundPayment->allowProcessBefore($cartId);
            $this->_payments[$payment] = $foundPayment;
            return $result;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function setAvailablePayment($payment, $paymentClass)
    {
        $this->availablePayments[$payment] = $paymentClass;
        return $this;
    }
}
